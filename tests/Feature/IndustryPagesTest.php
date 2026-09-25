<?php

namespace Tests\Feature;

use App\Models\Industry;
use App\Models\ProductType;
use App\Models\Project;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndustryPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_hub_lists_every_published_sector(): void
    {
        $this->seed(ContentSeeder::class);
        Industry::create(['name' => 'Retired sector', 'is_published' => false]);

        $this->get('/industries')
            ->assertOk()
            ->assertSee('Every sector')
            ->assertSee('9 sectors')                 // counted, not hard-coded
            ->assertSee('Battery')
            ->assertSee('Acid &amp; heat resistant', false)  // the note from ContentSeeder
            ->assertSee('/industries/pizza-qsr', false)
            ->assertDontSee('Retired sector');
    }

    public function test_every_seeded_sector_has_a_card_photograph(): void
    {
        $this->seed(ContentSeeder::class);

        $missing = Industry::published()->ordered()->get()
            ->filter(fn (Industry $industry) => blank($industry->card_image))
            ->pluck('slug');

        $this->assertTrue($missing->isEmpty(), 'sectors without a card image: '.$missing->join(', '));
    }

    public function test_a_sector_page_is_built_from_its_own_record(): void
    {
        $industry = Industry::create([
            'name' => 'Paints',
            'note' => 'Solvent resistance',
            'excerpt' => 'Labels for tins that get wiped with thinner.',
            'challenges' => '<div>Solvent lifts most adhesives.</div><ul><li>Wipe-down with thinner</li></ul>',
            'compliance_notes' => '<ul><li>Hazard marks placed inside the trim tolerance.</li></ul>',
        ]);

        $this->get('/industries/paints')
            ->assertOk()
            ->assertSee('Paints packaging.')
            ->assertSee('Solvent resistance')
            ->assertSee('Labels for tins that get wiped with thinner.')
            ->assertSee('Solvent lifts most adhesives.')
            ->assertSee('Wipe-down with thinner')
            ->assertSee('Hazard marks placed inside the trim tolerance.')
            ->assertSee('What the rulebook says.');

        $this->assertSame('paints', $industry->fresh()->slug);
    }

    public function test_empty_blocks_are_hidden_and_the_numbering_closes_up_behind_them(): void
    {
        Industry::create(['name' => 'Bare sector', 'note' => 'Nothing written yet']);

        // No challenges, no compliance, no case studies: only "what we supply"
        // and "other sectors" are left, and they number 01 and 02.
        $this->get('/industries/bare-sector')
            ->assertOk()
            ->assertDontSee('The failure we design against.')
            ->assertDontSee('What the rulebook says.')
            ->assertDontSee('What it looked like.')
            ->assertSee('01 / What we supply')
            ->assertSee('Nothing written yet');
    }

    public function test_the_supply_list_drops_products_named_after_another_sector(): void
    {
        $this->seed(ContentSeeder::class);

        // Asserted on the view data: "Battery labels" is also a nav and footer link,
        // so the rendered HTML cannot tell us which list it came from.
        $this->get('/industries/pharma')->assertOk()->assertViewHas(
            'supplies',
            fn ($supplies) => $supplies->contains('Mono cartons') && ! $supplies->contains('Battery labels'),
        );

        $this->get('/industries/battery')->assertOk()->assertViewHas(
            'supplies',
            fn ($supplies) => $supplies->contains('Battery labels'),
        );
    }

    public function test_case_studies_are_scoped_to_the_sector(): void
    {
        $paints = Industry::create(['name' => 'Paints']);
        $other = Industry::create(['name' => 'Toys']);
        ProductType::create(['name' => 'Sheet labels']);

        Project::create(['title' => 'Tin label rework', 'is_published' => true])->industries()->attach($paints);
        Project::create(['title' => 'Board game box', 'is_published' => true])->industries()->attach($other);

        $this->get('/industries/paints')
            ->assertOk()
            ->assertSee('Tin label rework')
            ->assertDontSee('Board game box');
    }

    public function test_the_cta_carries_the_sector_into_the_rfq(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/industries/pizza-qsr')
            ->assertOk()
            ->assertSee('/get-a-quote?industry=Pizza%20%26%20QSR', false);

        // …and the quote form opens with it chosen
        $this->get('/get-a-quote?industry=Pizza+%26+QSR')
            ->assertOk()
            ->assertSee('<option value="Pizza &amp; QSR" selected>Pizza &amp; QSR</option>', false);
    }

    public function test_an_unpublished_sector_is_not_reachable(): void
    {
        Industry::create(['name' => 'Draft sector', 'is_published' => false]);

        $this->get('/industries/draft-sector')->assertNotFound();
        $this->get('/industries/nothing-like-this')->assertNotFound();
    }

    public function test_seo_fields_override_the_defaults_when_set(): void
    {
        Industry::create(['name' => 'Paints', 'seo_title' => 'Paint tin labels in Haryana', 'seo_description' => 'Solvent-proof tin labels.']);

        $this->get('/industries/paints')
            ->assertSee('<title>Paint tin labels in Haryana · Powerstik</title>', false)
            ->assertSee('Solvent-proof tin labels.');
    }
}
