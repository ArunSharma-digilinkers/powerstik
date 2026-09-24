<?php

namespace Tests\Feature;

use App\Models\JobOpening;
use App\Models\Setting;
use App\Models\TimelineEvent;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_renders_seeded_content_without_placeholder_claims(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/about')
            ->assertOk()
            ->assertSee('It started at a')
            ->assertSee('Every machine has a reason.')
            ->assertSee('Founded as a design setup')
            ->assertSee('Two minds. One spark.')       // the brochure's own headline
            ->assertSee('Amit Sharma')
            ->assertSee('Founder &amp; head of Design India', false)
            ->assertSee('achieve the No.1 position in the market') // bio, from the brochure
            ->assertSee('team/', false)                 // seeded portrait
            ->assertDontSee('A delivery date is a promise') // pull-quotes stay demo-only
            ->assertSee('No open roles right now')     // careers empty state
            ->assertDontSee('Paper is a recyclable material.') // sustainability hidden until entered
            ->assertDontSee('360° walkthrough');
    }

    public function test_sections_are_numbered_in_order_of_what_is_shown(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertDontSee('Every machine has a reason.')
            ->assertSeeInOrder(['01 / Design India', '02 / Our people', '03 / Quality', '04 / Infrastructure', '05 / Careers']);
    }

    public function test_year_query_preselects_a_milestone(): void
    {
        TimelineEvent::create(['year' => 2002, 'title' => 'Founded']);
        TimelineEvent::create(['year' => 2017, 'title' => 'The paper']);

        $this->get('/about?year=2017')->assertSee('active: 1,', false);
        $this->get('/about?year=1999')->assertSee('active: 0,', false);
    }

    public function test_settings_drive_roles_certifications_and_sustainability(): void
    {
        JobOpening::create(['title' => 'Packaging designer', 'department' => 'Design studio', 'employment_type' => 'full_time']);
        JobOpening::create(['title' => 'Closed role', 'is_open' => false]);
        Setting::put([
            'careers_email' => 'jobs@example.com',
            'certifications' => "ISO 9001:2015\nFSC",
            'sustainability' => 'Waste board baled: Returned to the mill.',
        ]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('Design studio · Full-time')
            ->assertSee('mailto:jobs@example.com?subject=Application%3A%20Packaging%20designer', false)
            ->assertDontSee('Closed role')
            ->assertSee('ISO 9001:2015')
            ->assertSee('Returned to the mill.');
    }

    public function test_about_us_redirects_to_about(): void
    {
        $this->get('/about-us')->assertRedirect('/about')->assertStatus(301);
    }
}
