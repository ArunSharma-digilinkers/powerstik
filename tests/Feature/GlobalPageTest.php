<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ExportCountry;
use App\Models\Lead;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Once;
use Tests\TestCase;

class GlobalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_markets_come_from_the_database_not_from_the_copy(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/global')
            ->assertOk()
            ->assertSee('Your port,')
            ->assertSee('11 markets')
            ->assertSee('Nigeria')
            ->assertSee('Battery labels')     // the per-country note
            ->assertDontSee('Eleven flags');  // the headline must not hard-code a count

        ExportCountry::create(['name' => 'Kenya', 'iso2' => 'KE', 'note' => 'Cartons']);

        // Site::exportCountries() memoises with once(), which survives between
        // requests inside a single test but not between real ones.
        Once::flush();

        $this->get('/global')->assertSee('12 markets')->assertSee('Kenya');
    }

    public function test_an_unpublished_market_is_left_off(): void
    {
        ExportCountry::create(['name' => 'Nepal', 'iso2' => 'NP']);
        ExportCountry::create(['name' => 'Atlantis', 'iso2' => 'AT', 'is_published' => false]);

        $this->get('/global')
            ->assertOk()
            ->assertSee('1 market')
            ->assertSee('Nepal')
            ->assertDontSee('Atlantis');
    }

    public function test_the_map_artwork_says_which_markets_it_predates(): void
    {
        // docs/design/brand.md: the brochure map carries nine pins, the site lists
        // eleven, and a redrawn map has been asked for. The page must not imply
        // the artwork is current.
        $this->seed(ContentSeeder::class);

        $this->get('/global')
            ->assertOk()
            ->assertSee('media/home/export-map.webp', false)
            ->assertSee('shows the nine markets on our company brochure');
    }

    public function test_export_clients_appear_only_with_permission_and_a_market(): void
    {
        $nigeria = ExportCountry::create(['name' => 'Nigeria', 'iso2' => 'NG']);

        $named = Client::create(['name' => 'Named Co', 'export_country_id' => $nigeria->id, 'show_logo' => true]);
        Client::create(['name' => 'Unpermitted Co', 'export_country_id' => $nigeria->id, 'show_logo' => false]);
        Client::create(['name' => 'Domestic Co', 'show_logo' => true]);

        $this->get('/global')
            ->assertOk()
            ->assertSee('Named Co')
            ->assertDontSee('Unpermitted Co')
            ->assertDontSee('Domestic Co');

        // With nobody nameable the whole section goes, and the numbering closes up
        $named->update(['show_logo' => false]);
        $this->get('/global')->assertDontSee('Who we ship to.')->assertSee('03 / International enquiry');
    }

    public function test_the_embedded_enquiry_files_a_lead_and_bounces_errors_back_here(): void
    {
        $this->seed(ContentSeeder::class);

        $this->from('/global')
            ->post('/enquiry/international', ['name' => 'Ade', 'email' => 'ade@example.com', 'phone' => '+234 800 000'])
            ->assertRedirect('/global#form')
            ->assertSessionHasErrors(['country', 'product_type'], null, 'international');

        $this->post('/enquiry/international', [
            'name' => 'Ade', 'email' => 'ade@example.com', 'phone' => '+234 800 000',
            'country' => 'Nigeria', 'port' => 'Apapa, Lagos', 'product_type' => 'Roll labels', 'incoterm' => 'CIF',
        ])->assertRedirect('/thank-you/international');

        $lead = Lead::sole();
        $this->assertSame('international', $lead->type);
        $this->assertSame('Nigeria', $lead->country);          // a column, not payload
        $this->assertSame('Apapa, Lagos', $lead->payload['port']);
        $this->assertSame('CIF', $lead->payload['incoterm']);
    }

    public function test_the_international_cta_now_points_at_this_page(): void
    {
        $this->assertSame('/global#enquiry', config('site.international_url'));

        $this->seed(ContentSeeder::class);
        $this->get('/')->assertOk()->assertSee('/global#enquiry', false);
        $this->get('/international-enquiry')->assertOk(); // still a direct landing page
    }
}
