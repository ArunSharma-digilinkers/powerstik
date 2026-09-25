<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\Project;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatteryLabelsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_flagship_page_carries_only_confirmed_client_claims(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/battery-labels')
            ->assertOk()
            ->assertSee('India’s most trusted battery sticker')   // the brochure's own line
            ->assertSee('25 years')
            ->assertSee('5,00,000')                               // labels_per_day setting
            ->assertSee('Sulphuric acid, 1.280 sp. gr.')          // the protocol Arun confirmed
            ->assertSee('60 °C')
            ->assertSee('24 hours')
            ->assertSee('Livfast')                                // the brochure clientele
            ->assertSee('Amaron')
            ->assertSee('media/home/acid-test.webp', false);      // the client's test-bench photograph
    }

    public function test_client_logos_stay_hidden_until_permission_is_given(): void
    {
        $client = Client::create(['name' => 'Confidential Co', 'logo' => 'clients/logo.webp', 'show_logo' => false]);

        $this->get('/battery-labels')->assertOk()
            ->assertSee('Confidential Co')          // the name is the brochure's, the logo is not ours to show
            ->assertDontSee('clients/logo.webp', false);

        $client->update(['show_logo' => true]);
        $this->get('/battery-labels')->assertSee('clients/logo.webp', false);
    }

    public function test_the_supplied_logos_are_attached_and_shown(): void
    {
        $this->seed(ContentSeeder::class);

        $clients = Client::ordered()->get();

        // The sheet the client supplied covers 14 of the 15 names
        $this->assertSame(14, $clients->whereNotNull('logo')->count());
        $exide = $clients->firstWhere('name', 'Exide');
        $this->assertNotNull($exide->logo);

        // Permission is granted for every name; the flag simply waits on artwork
        // for the one the sheet does not cover, which falls back to type.
        $this->assertSame(15, $clients->where('show_logo', true)->count());
        $this->assertNull($clients->firstWhere('name', 'Unique Energos')->logo);

        $this->get('/battery-labels')
            ->assertOk()
            ->assertSee($exide->logo, false)
            ->assertSee('logo-mark', false)       // greyscale until hovered
            ->assertSee('Unique Energos');
    }

    public function test_sections_are_numbered_over_what_is_actually_shown(): void
    {
        // No clients and no case studies: the two optional sections drop out and
        // the numbering closes up behind them.
        $this->get('/battery-labels')
            ->assertOk()
            ->assertSeeInOrder(['01 / The paper', '02 / What goes on a battery', '03 / Sheet or roll', '04 / The box around them', '05 / Test it yourself'])
            ->assertDontSee('Our major clientele');
    }

    public function test_battery_case_studies_come_from_the_industry_relation(): void
    {
        $battery = Industry::create(['name' => 'Battery']);
        $other = Industry::create(['name' => 'Pharma']);

        $onTopic = Project::create(['title' => 'Terminal label rework', 'result_headline' => 'Returns down', 'is_published' => true]);
        $onTopic->industries()->attach($battery);

        $offTopic = Project::create(['title' => 'Blister carton', 'is_published' => true]);
        $offTopic->industries()->attach($other);

        $unpublished = Project::create(['title' => 'Draft case', 'is_published' => false]);
        $unpublished->industries()->attach($battery);

        $this->get('/battery-labels')
            ->assertOk()
            ->assertSee('Terminal label rework')
            ->assertSee('Returns down')
            ->assertDontSee('Blister carton')
            ->assertDontSee('Draft case');
    }

    public function test_the_embedded_form_files_a_sample_lead_and_bounces_errors_back_here(): void
    {
        $this->from('/battery-labels')
            ->post('/enquiry/sample', ['name' => 'Asha', 'email' => 'asha@example.com', 'phone' => '+91 90000 00000'])
            ->assertRedirect('/battery-labels#form')
            ->assertSessionHasErrors('application', null, 'sample');

        $this->post('/enquiry/sample', [
            'name' => 'Asha',
            'email' => 'asha@example.com',
            'phone' => '+91 90000 00000',
            'application' => '150Ah tubular battery',
            'address' => 'Plot 7, Pune',
        ])->assertRedirect('/thank-you/sample');

        $lead = Lead::sole();
        $this->assertSame('sample', $lead->type);
        $this->assertSame('150Ah tubular battery', $lead->payload['application']);
        $this->assertSame('Plot 7, Pune', $lead->payload['address']);
    }

    public function test_the_sample_cta_now_points_at_the_flagship_page(): void
    {
        $this->assertSame('/battery-labels#sample', config('site.sample_url'));

        $this->seed(ContentSeeder::class);
        $this->get('/')->assertOk()->assertSee('/battery-labels#sample', false);
    }
}
