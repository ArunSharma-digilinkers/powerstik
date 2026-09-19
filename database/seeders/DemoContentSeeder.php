<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * ILLUSTRATIVE content from the home page design: case studies, testimonials and
 * the founders' note. None of it is real, so it must never reach production.
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
    }
}
