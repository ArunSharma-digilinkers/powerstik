<?php

namespace Tests\Feature;

use App\Mail\LeadAcknowledged;
use App\Mail\LeadReceived;
use App\Models\Lead;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    /** A complete, valid RFQ for a sheet job. */
    private function quote(array $overrides = []): array
    {
        return array_merge([
            'product_type' => 'Sheet labels',
            'industry' => 'Battery',
            'technology' => 'Offset',
            'form' => 'Sheets',
            'size' => '90 × 45 mm',
            'quantity' => '50,000',
            'finish' => ['Spot UV'],
            'name' => 'Rahul Verma',
            'company' => 'Verma Batteries',
            'email' => 'rahul@example.com',
            'phone' => '+91 98100 00000',
            'city' => 'Sonipat',
            'message' => 'Repeat of last year’s job.',
        ], $overrides);
    }

    public function test_the_three_wizards_render_with_their_own_steps(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/get-a-quote')->assertOk()
            ->assertSee('Tell us the job.')
            ->assertSeeInOrder(['The job', 'Specification', 'You &amp; the artwork'], false)
            ->assertSee('Sheet labels')      // seeded product types
            ->assertSee('Roll specification');

        $this->get('/request-a-sample')->assertOk()
            ->assertSee('Test it yourself.')
            ->assertSee('What will it go on?')
            ->assertDontSee('Roll specification');

        $this->get('/international-enquiry')->assertOk()
            ->assertSee('Shipping to your port.')
            ->assertSee('Port of discharge')
            ->assertSee('Algeria')           // seeded export countries, as the datalist
            ->assertSee('11 markets');       // the counter, not a hard-coded number
    }

    public function test_a_quote_is_stored_split_between_columns_and_payload(): void
    {
        Mail::fake();
        Setting::put(['lead_recipients' => 'sales@powerstik.test, bad-address']);

        $this->post('/enquiry/quote', $this->quote())
            ->assertRedirect('/thank-you/quote')
            ->assertSessionHas('lead');

        $lead = Lead::sole();
        $this->assertSame('quote', $lead->type);
        $this->assertSame('new', $lead->status);
        $this->assertSame('Rahul Verma', $lead->name);
        $this->assertSame('Sonipat', $lead->city);

        // Everything that is not a column on `leads` lands in the payload
        $this->assertSame('Sheet labels', $lead->payload['product_type']);
        $this->assertSame(['Spot UV'], $lead->payload['finish']);
        $this->assertArrayNotHasKey('website', $lead->payload);
        $this->assertArrayNotHasKey('_token', $lead->payload);
        $this->assertArrayNotHasKey('name', $lead->payload);

        Mail::assertQueued(LeadReceived::class, fn ($mail) => $mail->hasTo('sales@powerstik.test'));
        Mail::assertQueued(LeadAcknowledged::class, fn ($mail) => $mail->hasTo('rahul@example.com'));
    }

    public function test_the_acknowledgement_carries_the_reference_and_the_alert_replies_to_the_enquirer(): void
    {
        Setting::put(['lead_recipients' => 'sales@powerstik.test', 'email' => 'hello@powerstik.test']);
        $this->post('/enquiry/quote', $this->quote());
        $lead = Lead::sole();

        $alert = new LeadReceived($lead);
        $alert->assertHasSubject('Quote request #'.$lead->id.' — Rahul Verma · Verma Batteries');
        $alert->assertHasReplyTo('rahul@example.com');
        $alert->assertSeeInHtml('Sheet labels');
        $alert->assertSeeInHtml('Spot UV');

        $receipt = new LeadAcknowledged($lead);
        $receipt->assertHasSubject('We have your quote request — Powerstik #'.$lead->id);
        $receipt->assertSeeInHtml('Thank you, Rahul Verma.');
        $receipt->assertSeeInHtml('#'.$lead->id);
    }

    public function test_validation_errors_come_back_in_a_bag_named_after_the_form(): void
    {
        Mail::fake();

        $this->from('/get-a-quote')
            ->post('/enquiry/quote', $this->quote(['quantity' => '', 'email' => 'not-an-email']))
            ->assertRedirect('/get-a-quote#form')
            ->assertSessionHasErrors(['quantity', 'email'], null, 'quote');

        $this->assertSame(0, Lead::count());
        Mail::assertNothingQueued();
    }

    public function test_roll_specs_are_only_required_for_roll_jobs(): void
    {
        $this->post('/enquiry/quote', $this->quote(['form' => 'Rolls']))
            ->assertSessionHasErrors(['core_size', 'winding'], null, 'quote');

        $this->post('/enquiry/quote', $this->quote(['form' => 'Rolls', 'core_size' => '3 inch', 'winding' => 'Out 4']))
            ->assertRedirect('/thank-you/quote');

        $this->assertSame('Out 4', Lead::sole()->payload['winding']);
    }

    public function test_artwork_is_stored_on_the_private_disk_and_capped(): void
    {
        Storage::fake('local');

        $this->post('/enquiry/quote', $this->quote([
            'artwork' => [
                UploadedFile::fake()->create('label.pdf', 120, 'application/pdf'),
                UploadedFile::fake()->create('dieline.pdf', 40, 'application/pdf'),
            ],
        ]))->assertRedirect('/thank-you/quote');

        $lead = Lead::sole();
        $this->assertCount(2, $lead->files);
        $this->assertSame('label.pdf', $lead->files->first()->original_name);
        Storage::disk('local')->assertExists($lead->files->first()->path);

        // An executable is not artwork
        $this->post('/enquiry/quote', $this->quote(['artwork' => [UploadedFile::fake()->create('payload.php', 10)]]))
            ->assertSessionHasErrors('artwork.0', null, 'quote');
    }

    public function test_a_filled_honeypot_files_the_lead_as_spam_and_sends_nothing(): void
    {
        Mail::fake();
        Setting::put(['lead_recipients' => 'sales@powerstik.test']);

        $this->post('/enquiry/quote', $this->quote(['website' => 'http://spam.example']))
            ->assertRedirect('/thank-you/quote');

        $this->assertSame('spam', Lead::sole()->status);
        Mail::assertNothingQueued();
    }

    public function test_campaign_tags_from_the_landing_page_reach_the_lead(): void
    {
        $this->get('/?utm_source=google&utm_campaign=battery-labels')->assertOk();
        $this->post('/enquiry/quote', $this->quote());

        $this->assertSame(
            ['utm_source' => 'google', 'utm_campaign' => 'battery-labels'],
            Lead::sole()->utm,
        );
    }

    public function test_contact_carries_both_forms_and_each_keeps_its_own_errors(): void
    {
        $this->get('/contact')->assertOk()
            ->assertSee('Talk to a person.')
            ->assertSee('id="message"', false)
            ->assertSee('id="callback"', false)
            ->assertSee('2184, Sector-38'); // the address setting, not hard-coded copy

        // The callback form needs a phone, not an email
        $this->from('/contact')
            ->post('/enquiry/callback', ['name' => 'Asha', 'phone' => ''])
            ->assertRedirect('/contact#callback')
            ->assertSessionHasErrors('phone', null, 'callback')
            ->assertSessionDoesntHaveErrors('phone', null, 'contact');

        $this->post('/enquiry/callback', ['name' => 'Asha', 'phone' => '+91 99999 11111', 'preferred_time' => 'Morning (9–12)'])
            ->assertRedirect('/thank-you/callback');

        $lead = Lead::sole();
        $this->assertSame('callback', $lead->type);
        $this->assertNull($lead->email);
        $this->assertSame('Morning (9–12)', $lead->payload['preferred_time']);
    }

    public function test_a_contact_message_is_required_but_a_phone_number_is_not(): void
    {
        $this->from('/contact')
            ->post('/enquiry/contact', ['name' => 'Asha', 'email' => 'asha@example.com'])
            ->assertRedirect('/contact#message')
            ->assertSessionHasErrors('message', null, 'contact');

        $this->post('/enquiry/contact', ['name' => 'Asha', 'email' => 'asha@example.com', 'message' => 'Do you print on PET?'])
            ->assertRedirect('/thank-you/contact');

        $this->assertNull(Lead::sole()->phone);
    }

    public function test_the_thank_you_page_shows_the_reference_and_fires_one_conversion(): void
    {
        $this->post('/enquiry/quote', $this->quote());
        $reference = Lead::sole()->id;

        $this->followingRedirects()
            ->post('/enquiry/quote', $this->quote())
            ->assertOk()
            ->assertSee('Got it.')
            ->assertSee('data-lead-event="quote"', false)
            ->assertSee('#'.($reference + 1));

        // Straight to the page with no submission behind it: no reference, still a page
        $this->get('/thank-you/contact')->assertOk()->assertSee('Message received.')->assertDontSee('Reference');
    }

    public function test_unknown_lead_types_are_not_routable(): void
    {
        $this->post('/enquiry/job_application', ['name' => 'Asha'])->assertNotFound();
        $this->get('/thank-you/anything')->assertNotFound();
    }

    public function test_every_conversion_link_on_the_live_pages_now_resolves(): void
    {
        $this->seed(ContentSeeder::class);

        foreach (['quote_url', 'callback_url', 'sample_url', 'international_url'] as $key) {
            $path = strtok(config("site.{$key}"), '#');
            $this->get($path)->assertOk();
        }
    }
}
