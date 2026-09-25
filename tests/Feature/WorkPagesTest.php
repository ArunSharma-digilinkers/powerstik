<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Industry;
use App\Models\ProductType;
use App\Models\Project;
use App\Models\Technology;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkPagesTest extends TestCase
{
    use RefreshDatabase;

    /** A battery roll-label case study and a pharma carton one. */
    private function portfolio(): array
    {
        $battery = Industry::create(['name' => 'Battery']);
        $pharma = Industry::create(['name' => 'Pharma']);
        $rolls = ProductType::create(['name' => 'Roll labels']);
        $cartons = ProductType::create(['name' => 'Mono cartons']);
        $flexo = Technology::create(['name' => 'Flexo']);

        $one = Project::create(['title' => 'Peeling label fixed', 'summary' => 'It lifted.', 'result_headline' => 'Zero rejections', 'is_published' => true]);
        $one->industries()->attach($battery);
        $one->productTypes()->attach($rolls);
        $one->technologies()->attach($flexo);

        $two = Project::create(['title' => 'Carton redesign', 'is_published' => true]);
        $two->industries()->attach($pharma);
        $two->productTypes()->attach($cartons);

        Project::create(['title' => 'Draft project', 'is_published' => false])->industries()->attach($battery);

        return [$one, $two];
    }

    public function test_the_listing_shows_published_work_only(): void
    {
        $this->portfolio();

        $this->get('/work')
            ->assertOk()
            ->assertSee('Problem, press,')
            ->assertSee('2 projects')
            ->assertSee('Peeling label fixed')
            ->assertSee('Zero rejections')
            ->assertSee('Carton redesign')
            ->assertDontSee('Draft project');
    }

    public function test_filters_are_plain_urls_and_combine(): void
    {
        $this->portfolio();

        $this->get('/work?industry=battery')
            ->assertOk()
            ->assertSee('Peeling label fixed')
            ->assertDontSee('Carton redesign');

        // Two facets at once, both matching the same project
        $this->get('/work?industry=battery&tech=flexo')
            ->assertOk()
            ->assertSee('Peeling label fixed');

        // Two facets that no single project satisfies
        $this->get('/work?industry=pharma&tech=flexo')
            ->assertOk()
            ->assertSee('Nothing here yet under that combination.')
            ->assertDontSee('Peeling label fixed');
    }

    public function test_a_facet_with_nothing_behind_it_is_not_offered(): void
    {
        $this->portfolio();
        ProductType::create(['name' => 'Never used']);
        Industry::create(['name' => 'Withdrawn', 'is_published' => false])
            ->projects()->attach(Project::first());

        $this->get('/work')
            ->assertOk()
            ->assertSee('Roll labels')
            ->assertDontSee('Never used')      // no projects behind it
            ->assertDontSee('Withdrawn');      // unpublished sector
    }

    public function test_facet_counts_narrow_with_the_other_filters(): void
    {
        $this->portfolio();

        // Unfiltered, both sectors are offered
        $this->get('/work')->assertSee('Battery')->assertSee('Pharma');

        // With flexo applied, pharma has nothing behind it, so it is not offered
        $this->get('/work?tech=flexo')
            ->assertOk()
            ->assertSee('Battery')
            ->assertDontSee('Pharma');
    }

    public function test_a_selected_facet_stays_visible_even_when_it_narrows_to_nothing(): void
    {
        $this->portfolio();

        // Otherwise the chip you need to click to undo the filter disappears
        $this->get('/work?industry=pharma&tech=flexo')
            ->assertOk()
            ->assertSee('Nothing here yet under that combination.')
            ->assertSee('Pharma')
            ->assertSee('Flexo')
            ->assertSee('Clear filters');
    }

    public function test_a_case_study_tells_its_story_only_when_it_is_written(): void
    {
        [$one] = $this->portfolio();
        $one->update([
            'is_case_study' => true,
            'problem' => '<div>The label lifted at one corner.</div>',
            'solution' => '<div>Moved it to our own paper.</div>',
        ]);

        $this->get('/work/peeling-label-fixed')
            ->assertOk()
            ->assertSee('Peeling label fixed')
            ->assertSee('The label lifted at one corner.')
            ->assertSee('Moved it to our own paper.')
            ->assertSeeInOrder(['Problem', 'What we did'])
            ->assertDontSee('>Result<', false);  // the empty third column is not printed
    }

    public function test_the_story_is_hidden_entirely_when_it_is_not_a_case_study(): void
    {
        [$one] = $this->portfolio();
        $one->update(['problem' => '<div>Written but not flagged.</div>', 'is_case_study' => false]);

        $this->get('/work/peeling-label-fixed')
            ->assertOk()
            ->assertDontSee('Written but not flagged.')
            ->assertDontSee('What actually happened.');
    }

    public function test_a_client_is_named_only_with_permission(): void
    {
        [$one] = $this->portfolio();
        $client = Client::create(['name' => 'Confidential Co', 'show_logo' => false]);
        $one->update(['client_id' => $client->id]);

        $this->get('/work/peeling-label-fixed')->assertOk()->assertDontSee('For Confidential Co');

        $client->update(['show_logo' => true]);
        $this->get('/work/peeling-label-fixed')->assertSee('For Confidential Co');
    }

    public function test_tags_link_back_into_the_filtered_listing(): void
    {
        [$one] = $this->portfolio();

        $this->get('/work/peeling-label-fixed')
            ->assertOk()
            ->assertSee('/work?industry=battery', false)
            ->assertSee('/work?product=roll-labels', false)
            ->assertSee('/work?tech=flexo', false);
    }

    public function test_related_work_comes_from_the_shared_sector(): void
    {
        [$one, $two] = $this->portfolio();
        $three = Project::create(['title' => 'Another battery job', 'is_published' => true]);
        $three->industries()->attach(Industry::where('name', 'Battery')->first());

        $this->get('/work/peeling-label-fixed')
            ->assertOk()
            ->assertSee('Another battery job')
            ->assertDontSee('Carton redesign')
            ->assertDontSee('Draft project');
    }

    public function test_the_portfolio_links_on_the_live_pages_resolve(): void
    {
        $this->seed(ContentSeeder::class);
        $featured = Project::create(['title' => 'Featured job', 'is_published' => true, 'is_featured' => true]);

        $this->get('/')->assertOk()
            ->assertSee('/work"', false)                 // "See full portfolio"
            ->assertSee('/work/featured-job', false);

        $this->get('/work/featured-job')->assertOk();
    }

    public function test_an_unpublished_project_is_not_reachable(): void
    {
        $this->portfolio();

        $this->get('/work/draft-project')->assertNotFound();
        $this->get('/work/nothing-like-this')->assertNotFound();
    }
}
