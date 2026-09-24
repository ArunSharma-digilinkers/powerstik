<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\JobOpening;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\TimelineEvent;
use Illuminate\Database\Seeder;

/**
 * ILLUSTRATIVE content from the home and About page designs: case studies, testimonials,
 * the founders' note, the story milestones after 2002, leadership bios and quotes, open
 * roles and sustainability claims. None of it is real, so it must never reach production.
 * Local only: php artisan db:seed --class=DemoContentSeeder
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('DemoContentSeeder holds placeholder copy and will not run in production.');

            return;
        }

        $cases = [
            ['A label that stopped peeling in transit', 'Battery', 'Battery · Case study', 'Electrolyte splash was lifting a client’s existing labels before the batteries reached the dealer.', 'Zero field rejections across 1.2M units'],
            ['9-ply that survives a delivery fleet', 'Pizza & QSR', 'QSR · Corrugated', 'Grease-resistant print and a flute spec rebuilt for stacking in bike boxes.', 'Damage claims down 60%'],
            ['Legible at 6pt, compliant in three states', 'Pharma', 'Pharma · Design + print', 'Carton redesign that fit mandated copy without losing the brand block.', 'Approved first submission'],
        ];
        foreach ($cases as $i => [$title, $industry, $tag, $summary, $result]) {
            $project = Project::firstOrCreate(['title' => $title], [
                'card_tag' => $tag, 'summary' => $summary, 'result_headline' => $result,
                'is_case_study' => true, 'is_featured' => true, 'sort' => $i + 1,
            ]);
            $project->industries()->syncWithoutDetaching(Industry::where('name', $industry)->pluck('id'));
        }

        $quotes = [
            ['They sent a mockup the same evening and the pallet in four days. We stopped shopping around.', 'Procurement head', 'Battery manufacturer, Gurugram'],
            ['The colour on our reorder matched the batch from two years ago. For us that is the whole job.', 'Brand manager', 'Personal care, Delhi NCR'],
            ['Documentation was right the first time, which almost never happens with a new supplier.', 'Import manager', 'Distributor, Kampala'],
        ];
        foreach ($quotes as $i => [$quote, $person, $role]) {
            Testimonial::firstOrCreate(['quote' => $quote], ['person' => $person, 'role' => $role, 'sort' => $i + 1]);
        }

        if (blank(Setting::get('founders_quote'))) {
            Setting::put(['founders_quote' => 'In 2002 we were two brothers with a design table and borrowed time on someone else’s press. We kept saying yes to work we couldn’t yet produce — then bought the machine that could. Twenty-four years later the design table is still where every job starts.']);
        }

        $this->aboutPage();
    }

    private function aboutPage(): void
    {
        // Only the 2002 founding is confirmed by the brief; the later years and stories are the design's reconstruction.
        $milestones = [
            [2002, 'The beginning', 'A design table and borrowed press time', 'Amit and Sumit Sharma set up Design India as a small design consultancy in Haryana — logos, labels and packaging artwork for local manufacturers. Every job was printed by somebody else, and every job came back slightly wrong.', 'Design India founded · Haryana'],
            [2006, 'First press', 'We stopped sending work out', 'The first offset press arrived because a battery client needed a run we could not trust a vendor with. Powerstik became the name on the invoice for anything printed in-house.', 'Powerstik® brand established'],
            [2010, 'Heidelberg SM-74', 'Five colours, and a real carton business', 'The Heidelberg SM-74 turned occasional print into a production line. Mono cartons for pharma and cosmetics followed within the year.', 'Heidelberg SM-74 installed'],
            [2014, 'Corrugation', 'From mono carton to 9-ply', 'A full corrugation line meant we could ship the box as well as the label on it. QSR and appliance clients arrived because one supplier could now do both.', 'Corrugation line commissioned'],
            [2017, 'The paper', 'Battery label paper, developed in-house', 'Existing label stock kept lifting under electrolyte splash and bonnet heat. We developed our own acid- and heat-resistant paper for lead-acid production lines — still our flagship product.', 'Proprietary substrate developed'],
            [2019, 'Roll labels', 'Mark Andy E5 and automatic applicators', 'Flexo roll labels let us supply clients running automatic applicators — defined core size and winding direction, reel after reel.', 'Mark Andy E5 flexo installed'],
            [2022, 'Going out', 'First export consignment', 'A battery manufacturer in Nepal placed the first export order. Seven more countries followed. Export documentation is now handled by the same coordinators who handle domestic jobs.', 'Exports begin · now 11 countries'],
        ];
        foreach ($milestones as [$year, $kicker, $title, $body, $meta]) {
            $event = TimelineEvent::where('year', $year)->first();
            if (! $event) {
                TimelineEvent::create(compact('year', 'kicker', 'title', 'body', 'meta'));
            } elseif ($event->body === 'Powerstik began as a small design studio.') {
                $event->update(compact('kicker', 'title', 'body', 'meta')); // replace the brief's one-line stub
            }
        }

        // The bios are the client's own, so they ship from ContentSeeder. These pull-quotes are
        // still the design's invention — written in their likely voice, never approved by them.
        $quotes = [
            'Amit Sharma' => 'A delivery date is a promise, not an estimate.',
            'Sumit Sharma' => 'Design is the reason they call us. Delivery is the reason they call again.',
        ];
        foreach ($quotes as $name => $quote) {
            TeamMember::where('name', $name)->whereNull('quote')->update(['quote' => $quote]);
        }

        $roles = [
            ['Packaging designer', 'Design studio'],
            ['Offset machine operator', 'Production'],
            ['Client coordinator', 'Sales & coordination'],
            ['Quality inspector', 'Quality & dispatch'],
        ];
        foreach ($roles as $i => [$title, $department]) {
            JobOpening::firstOrCreate(['title' => $title], [
                'department' => $department, 'location' => 'Haryana', 'employment_type' => 'full_time', 'sort' => $i + 1,
            ]);
        }

        if (blank(Setting::get('sustainability'))) {
            Setting::put(['sustainability' => implode("\n", [
                'Waste board baled: Corrugated trim returned to the mill rather than landfilled.',
                'Make-ready reused: Set-up sheets become client samples and internal proofs.',
                'Recycled board specified: Offered as default wherever the job permits it.',
                'Right-first-time: Fewer reprints is the largest single saving we make.',
            ])]);
        }
    }
}
