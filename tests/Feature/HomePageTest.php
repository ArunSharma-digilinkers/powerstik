<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DemoContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_seeded_content(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Delivered like a factory.')
            ->assertSee('Acid & heat resistant')        // industry card note
            ->assertSee('industries/', false)            // seeded card image
            ->assertSee('Eleven countries, one standard.')
            ->assertSee('Livguard')                     // client wall
            ->assertSee('Amit Sharma')
            ->assertDontSee('Problem, press, result.'); // no featured work until it exists
    }

    public function test_featured_case_studies_and_counters_come_from_the_database(): void
    {
        $this->seed(ContentSeeder::class);
        Project::create(['title' => 'Real job', 'card_tag' => 'Battery · Case study', 'result_headline' => 'Zero rejections', 'is_featured' => true]);
        Project::create(['title' => 'Hidden job', 'is_featured' => true, 'is_published' => false]);
        Setting::put(['people' => '250+']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Problem, press, result.')
            ->assertSee('Zero rejections')
            ->assertDontSee('Hidden job')
            ->assertSee('250+');
    }

    public function test_brochure_facts_reach_the_page(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Livfast')                                 // brochure clientele
            ->assertSee('Su-Kam')
            ->assertSee('UAE')                                     // brochure export map
            ->assertSee('Nigeria')
            ->assertSee('Zimbabwe')
            ->assertSee('India’s most trusted battery sticker', false)
            ->assertSee('Twenty-five years of core experience')
            ->assertSee('5,00,000 labels a day')
            ->assertSee('Rai Industrial Estate')                   // brochure address
            ->assertSee('+91 130 310 0105');                       // brochure landline
    }

    public function test_the_flagship_section_carries_the_test_bench_photograph(): void
    {
        // The proof band only renders while the client's photo is in public/media/home.
        $this->assertFileExists(public_path('media/home/acid-test.webp'));

        $this->get('/')
            ->assertOk()
            ->assertSee('From the test bench')
            ->assertSee('media/home/acid-test.webp')
            // The client's own test protocol, so a copy edit cannot quietly soften it.
            ->assertSee('Sulphuric acid, 1.280 sp. gr.')
            ->assertSee('24 hours');
    }

    public function test_whatsapp_button_uses_the_configured_number(): void
    {
        // The brochure's mobile is the default, and Settings overrides it.
        $this->get('/')->assertSee('https://wa.me/919899269999', false);

        Setting::put(['whatsapp' => '919812345678', 'whatsapp_message' => 'Hi there']);

        $this->get('/')->assertSee('https://wa.me/919812345678?text=Hi%20there', false);
    }

    public function test_the_button_disappears_when_no_whatsapp_number_is_set(): void
    {
        Setting::put(['whatsapp' => '']);

        $this->get('/')->assertDontSee('https://wa.me/', false);
    }

    public function test_demo_content_never_seeds_in_production(): void
    {
        $this->app['env'] = 'production';

        (new DemoContentSeeder)->run();

        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('testimonials', 0);
    }
}
