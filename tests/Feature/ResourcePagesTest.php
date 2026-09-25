<?php

namespace Tests\Feature;

use App\Models\Download;
use App\Models\Faq;
use App\Models\GlossaryTerm;
use App\Models\Lead;
use App\Models\Post;
use App\Models\PostCategory;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourcePagesTest extends TestCase
{
    use RefreshDatabase;

    private function pdf(string $name = 'profile.pdf'): Download
    {
        Storage::fake('public_uploads');
        Storage::disk('public_uploads')->put("downloads/{$name}", '%PDF-1.4 fake');

        return Download::create(['title' => 'Company profile', 'category' => 'company', 'file' => "downloads/{$name}"]);
    }

    public function test_the_hub_offers_only_what_has_something_behind_it(): void
    {
        $this->seed(ContentSeeder::class);   // seeds faqs and glossary terms, no downloads or posts

        $this->get('/resources')
            ->assertOk()
            ->assertSee('The bits you')
            ->assertSee('Material guide')
            ->assertSee('FAQs')
            ->assertDontSee('>Downloads<', false)   // nothing uploaded
            ->assertDontSee('>Insights<', false);   // nothing published

        $this->pdf();
        Post::create(['title' => 'A note', 'published_at' => now()->subDay()]);

        $this->get('/resources')->assertSee('Downloads')->assertSee('Insights');
    }

    public function test_faqs_group_by_category_and_publish_structured_data(): void
    {
        $this->seed(ContentSeeder::class);
        Faq::create(['question' => 'Withdrawn question', 'answer' => '<div>No.</div>', 'is_published' => false]);

        $response = $this->get('/resources/faqs')->assertOk()
            ->assertSee('What is the minimum order quantity?')
            ->assertSee('Orders')
            ->assertSee('Export')
            ->assertDontSee('Withdrawn question')
            ->assertSee('application/ld+json', false)
            ->assertSee('FAQPage', false);

        // The schema carries the answer as text, not as markup
        $this->assertStringContainsString('Within 2–5 days of artwork approval', $response->getContent());
        $this->assertStringNotContainsString('"text":"<div>', $response->getContent());
    }

    public function test_the_material_guide_indexes_only_letters_that_have_a_term(): void
    {
        GlossaryTerm::create(['term' => 'Flute', 'definition' => '<div>The wavy bit.</div>']);
        GlossaryTerm::create(['term' => 'GSM', 'definition' => '<div>Grams per square metre.</div>']);

        $this->get('/resources/material-guide')
            ->assertOk()
            ->assertSee('#letter-F', false)
            ->assertSee('#letter-G', false)
            ->assertDontSee('#letter-Z', false)
            ->assertSee('The wavy bit.')
            ->assertSeeInOrder(['Flute', 'GSM']);   // alphabetical
    }

    public function test_an_ungated_download_is_served_directly(): void
    {
        $download = $this->pdf();

        $this->get('/resources/downloads')->assertOk()->assertSee('Download the PDF');

        $this->get(route('downloads.file', $download))
            ->assertOk()
            ->assertDownload('Company profile.pdf');
    }

    public function test_a_gated_download_is_withheld_until_the_form_is_filled(): void
    {
        Mail::fake();
        $download = $this->pdf();
        $download->update(['requires_email' => true]);

        // The raw file route is refused without a signature
        $this->get(route('downloads.file', $download))->assertForbidden();

        $this->get('/resources/downloads')->assertOk()->assertSee('Send me the PDF');

        // A bad submission comes back in this download's own error bag
        $this->from('/resources/downloads')
            ->post(route('downloads.request', $download), ['name' => 'Asha'])
            ->assertRedirect('/resources/downloads#download-'.$download->id)
            ->assertSessionHasErrors('email', null, 'download-'.$download->id);

        $this->assertSame(0, Lead::count());

        // A good one files the lead and hands back a signed link that works
        $response = $this->post(route('downloads.request', $download), [
            'name' => 'Asha Rao', 'email' => 'asha@example.com', 'company' => 'Rao Batteries',
        ])->assertRedirect();

        $lead = Lead::sole();
        $this->assertSame('download', $lead->type);
        $this->assertSame('Company profile', $lead->payload['download']);
        $this->assertNull($lead->phone);   // a download gate does not ask for one

        $this->get($response->headers->get('Location'))->assertOk()->assertDownload('Company profile.pdf');
    }

    public function test_a_gated_link_expires(): void
    {
        $download = $this->pdf();
        $download->update(['requires_email' => true]);

        $url = $this->post(route('downloads.request', $download), ['name' => 'Asha', 'email' => 'asha@example.com'])
            ->headers->get('Location');

        $this->travel(16)->minutes();

        $this->get($url)->assertForbidden();
    }

    public function test_a_withdrawn_or_fileless_download_is_not_offered(): void
    {
        $this->pdf();
        Download::create(['title' => 'No file yet', 'category' => 'company']);
        Download::create(['title' => 'Withdrawn', 'file' => 'downloads/x.pdf', 'is_published' => false]);

        $this->get('/resources/downloads')
            ->assertOk()
            ->assertSee('Company profile')
            ->assertDontSee('No file yet')
            ->assertDontSee('Withdrawn');
    }

    public function test_the_empty_downloads_page_still_does_a_job(): void
    {
        // Home and the footer both link here, so it must not read as broken
        $this->get('/resources/downloads')
            ->assertOk()
            ->assertSee('Nothing to download here yet.')
            ->assertSee('Ask for the profile')
            ->assertSee(route('resources.material-guide'), false);
    }

    public function test_insights_hides_drafts_and_future_posts(): void
    {
        $category = PostCategory::create(['name' => 'Materials']);
        Post::create(['title' => 'Published note', 'post_category_id' => $category->id, 'published_at' => now()->subDay()]);
        Post::create(['title' => 'Draft note', 'published_at' => null]);
        Post::create(['title' => 'Scheduled note', 'published_at' => now()->addWeek()]);

        $this->get('/insights')
            ->assertOk()
            ->assertSee('Published note')
            ->assertSee('1 post')
            ->assertDontSee('Draft note')
            ->assertDontSee('Scheduled note');

        $this->get('/insights/draft-note')->assertNotFound();
        $this->get('/insights/scheduled-note')->assertNotFound();
        $this->get('/insights/published-note')->assertOk()->assertSee('Article', false);
    }

    public function test_insight_categories_filter_and_only_appear_when_used(): void
    {
        $materials = PostCategory::create(['name' => 'Materials']);
        PostCategory::create(['name' => 'Never used']);
        Post::create(['title' => 'On adhesives', 'post_category_id' => $materials->id, 'published_at' => now()->subDay()]);
        Post::create(['title' => 'Uncategorised note', 'published_at' => now()->subDay()]);

        $this->get('/insights')->assertOk()->assertSee('Materials')->assertDontSee('Never used');

        $this->get('/insights?category=materials')
            ->assertOk()
            ->assertSee('On adhesives')
            ->assertDontSee('Uncategorised note');
    }

    public function test_the_client_zone_placeholder_is_a_page_not_a_404(): void
    {
        $this->get('/client/login')->assertOk()->assertSee('Not open');

        foreach (config('site.utility') as [$label, $path]) {
            $this->get($path)->assertOk();
        }

        foreach (config('site.footer.Resources') as [$label, $path]) {
            $this->get($path)->assertOk();
        }
    }
}
