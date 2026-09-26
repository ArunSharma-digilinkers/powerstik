<?php

namespace Tests\Feature;

use App\Http\Controllers\CapabilityController;
use App\Models\Machine;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapabilityPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_hub_lists_the_four_disciplines_and_counts_the_presses(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/capabilities')
            ->assertOk()
            ->assertSee('Nothing leaves')
            ->assertSee('Design &amp; branding', false)
            ->assertSee('Label printing')
            ->assertSee('Corrugated packaging')
            ->assertSee('Print technologies')
            ->assertSee('5 presses and lines')   // counted from the machines table
            ->assertSee('4 processes')
            ->assertSee('Mark Andy E5');         // the flexo group's names
    }

    public function test_every_discipline_page_renders_its_own_block(): void
    {
        $blocks = [
            'design-branding' => 'Drawn, shown, then made.',
            'label-printing' => 'Four numbers settle it.',
            'corrugated-packaging' => 'Ply is a load decision.',
            'printing-technologies' => 'When each one wins.',
        ];

        foreach ($blocks as $slug => $heading) {
            $response = $this->get("/capabilities/{$slug}")->assertOk()->assertSee($heading);

            // …and each one links to the other three plus the machine park
            foreach (array_keys($blocks) as $other) {
                $slug === $other
                    ? $response->assertDontSee('"/capabilities/'.$other.'"', false)
                    : $response->assertSee('/capabilities/'.$other, false);
            }

            $response->assertSee('/capabilities/machine-park', false);
        }
    }

    public function test_settings_fill_the_fact_strip_rather_than_hard_coded_numbers(): void
    {
        Setting::put(['moq' => '250 units', 'dispatch_days' => '4–6 days']);

        $this->get('/capabilities/label-printing')
            ->assertOk()
            ->assertSee('From 250')
            ->assertSee('4–6 days');
    }

    public function test_an_unknown_discipline_is_not_a_page(): void
    {
        $this->get('/capabilities/offset-printing')->assertNotFound();
        $this->get('/capabilities/machine-park')->assertOk(); // not read as a discipline
    }

    public function test_the_machine_park_groups_by_process_and_skips_empty_ones(): void
    {
        Machine::create(['name' => 'Heidelberg Speedmaster SM-74', 'category' => 'offset', 'make' => 'Heidelberg', 'model' => 'SM-74',
            'specs' => ['Max sheet' => '520 × 740 mm', 'Colours' => 'Five']]);
        Machine::create(['name' => 'Mark Andy E5', 'category' => 'flexo']);
        Machine::create(['name' => 'Retired press', 'category' => 'offset', 'is_published' => false]);

        $this->get('/capabilities/machine-park')
            ->assertOk()
            ->assertSee('Every press,')
            ->assertSee('Heidelberg · SM-74')
            ->assertSee('Max sheet')
            ->assertSee('520 × 740 mm')
            ->assertSeeInOrder(['01 / Offset', '02 / Flexo'])   // Machine::CATEGORIES order
            ->assertDontSee('Corrugation.')                      // nothing in that category
            ->assertDontSee('Retired press')
            ->assertViewHas('machines', fn ($machines) => $machines->flatten()->count() === 2);
    }

    public function test_the_makers_cutouts_are_never_paired_with_a_machine_record(): void
    {
        // docs/design/brand.md: one of those pictures is a different press from the
        // one the brochure credits, and a single Speedmaster photo cannot stand for two.
        Machine::create(['name' => 'Digital press', 'category' => 'digital']);

        $this->get('/capabilities/machine-park')
            ->assertOk()
            ->assertSee('Digital press')
            ->assertDontSee('media/machines/', false);
    }

    public function test_the_seeder_gives_each_press_its_own_description(): void
    {
        $this->seed(ContentSeeder::class);

        $descriptions = Machine::ordered()->pluck('description', 'name');

        $this->assertCount(5, $descriptions->unique(), 'every press should describe itself, not the first one');
        $this->assertSame('Large format, for long runs and heavy stock.', $descriptions['Heidelberg Speedmaster CD-102']);

        // The brochure credits Canon but the only press picture we hold is a Xerox,
        // so the digital press stays unbranded until the client settles it.
        $this->assertTrue($descriptions->has('Digital press'));
        $this->assertNull(Machine::where('name', 'Digital press')->value('make'));
    }

    public function test_an_admin_uploaded_photo_is_shown(): void
    {
        Machine::create(['name' => 'Mark Andy E5', 'category' => 'flexo', 'image' => 'machines/e5.webp']);

        $response = $this->get('/capabilities/machine-park')->assertSee('machines/e5.webp', false);

        // The well has to bound the photograph itself. A fixed-height grid well
        // did not: the implicit row is auto, so the image sized to its own
        // content and spilled over the name and the spec list underneath.
        $response->assertSee('aspect-[4/3] overflow-hidden', false);
    }

    public function test_every_capability_link_on_the_live_pages_resolves(): void
    {
        $this->seed(ContentSeeder::class);

        foreach (array_keys(CapabilityController::DISCIPLINES) as $slug) {
            $this->get("/capabilities/{$slug}")->assertOk();
        }

        foreach (config('site.footer.Capabilities') as [$label, $path]) {
            $this->get($path)->assertOk();
        }

        // The home grid and the machine-park teaser
        $this->get('/')->assertOk()
            ->assertSee('/capabilities/design-branding', false)
            ->assertSee('/capabilities/machine-park', false)
            ->assertDontSee('/about/infrastructure', false);
    }
}
