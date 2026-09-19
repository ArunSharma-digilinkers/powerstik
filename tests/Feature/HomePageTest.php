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
            ->assertSee('Eight countries, one standard.')
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

    public function test_whatsapp_button_uses_the_configured_number(): void
    {
        $this->get('/')->assertDontSee('https://wa.me/', false);

        Setting::put(['whatsapp' => '919812345678', 'whatsapp_message' => 'Hi there']);

        $this->get('/')->assertSee('https://wa.me/919812345678?text=Hi%20there', false);
    }

    public function test_demo_content_never_seeds_in_production(): void
    {
        $this->app['env'] = 'production';

        (new DemoContentSeeder)->run();

        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('testimonials', 0);
    }
}
