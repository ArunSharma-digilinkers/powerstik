<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\JobOpening;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\ProductType;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Technology;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * ILLUSTRATIVE content from the home and About page designs: case studies, testimonials,
 * the founders' note, leadership bios and quotes, open roles, sustainability claims and
 * the industry page copy. None of it is real, so it must never reach production.
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

        // title, industry, card tag, summary, result headline, product types,
        // technologies, then Problem / What we did / Result.
        $cases = [
            [
                'A label that stopped peeling in transit', 'Battery', 'Battery · Case study',
                'Electrolyte splash was lifting a client’s existing labels before the batteries reached the dealer.',
                'Zero field rejections across 1.2M units',
                ['Battery labels', 'Roll labels'], ['Offset'],
                'Batteries were arriving at dealers with the main label lifted at one corner and the terminal label bleeding. The dealer was refusing them on sight, so a label worth a few paise was writing off a battery.',
                'We ran the existing stock and ours side by side in full-strength electrolyte at bonnet temperature for a day, and showed the client both trays. The job moved onto our own battery paper, and the terminal label was redrawn a few millimetres clear of the post.',
                'Twelve months and 1.2 million units later, no field rejection has come back to the label. The client moved the rest of their range across the following quarter.',
            ],
            [
                '9-ply that survives a delivery fleet', 'Pizza & QSR', 'QSR · Corrugated',
                'Grease-resistant print and a flute spec rebuilt for stacking in bike boxes.',
                'Damage claims down 60%',
                ['Corrugated boxes'], ['Flexo'],
                'Boxes were going soft in the delivery bag. Stacked four high on a bike in monsoon season, the bottom box was arriving crushed and the print was smearing where the grease had come through.',
                'The flute was changed for one that holds its stack when it is warm and damp, and the printed face moved to a grease-resistant coating. We also cut the box down: it had been sized for a pizza half an inch larger than the one they actually make.',
                'Damage claims dropped by around 60% over the next two quarters, and the smaller box took a little off the freight as well.',
            ],
            [
                'Legible at 6pt, compliant in three states', 'Pharma', 'Pharma · Design + print',
                'Carton redesign that fit mandated copy without losing the brand block.',
                'Approved first submission',
                ['Mono cartons', 'Branding & design'], ['Offset'],
                'A product going into three states needed all of the mandated copy on a carton that was already full. The previous version had shrunk the type to fit, and one reviewer had already called it out.',
                'The studio rebuilt the carton from the dieline up, moving the mandated blocks to a panel of their own and rebuilding the brand block around what was left. Nothing went below the agreed point size.',
                'It went through on the first submission in all three states, and the same layout has carried four SKU additions since without another redesign.',
            ],
        ];
        foreach ($cases as $i => [$title, $industry, $tag, $summary, $result, $products, $technologies, $problem, $solution, $outcome]) {
            $project = Project::firstOrCreate(['title' => $title], [
                'card_tag' => $tag, 'summary' => $summary, 'result_headline' => $result,
                'is_case_study' => true, 'is_featured' => true, 'sort' => $i + 1,
            ]);

            $project->industries()->syncWithoutDetaching(Industry::where('name', $industry)->pluck('id'));
            $project->productTypes()->syncWithoutDetaching(ProductType::whereIn('name', $products)->pluck('id'));
            $project->technologies()->syncWithoutDetaching(Technology::whereIn('name', $technologies)->pluck('id'));

            if (blank($project->problem)) {
                $project->update([
                    'problem' => '<div>'.e($problem).'</div>',
                    'solution' => '<div>'.e($solution).'</div>',
                    'result' => '<div>'.e($outcome).'</div>',
                ]);
            }
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
        $this->industryPages();
        $this->insights();
    }

    private function aboutPage(): void
    {
        // The story milestones used to be invented here. The client gave us the real
        // seven on 2026-10-17, so they ship from ContentSeeder now — nothing to add.

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

    /**
     * Sector copy for the industry pages. The `note` on each industry is real —
     * it came from the home page design and ships in ContentSeeder. Everything
     * here is ours, written to show the template with copy in it.
     *
     * `what_we_supply` is deliberately left empty on every sector: the detail
     * page falls back to the product-type list, and leaving it blank keeps that
     * fallback under review. Only sectors with a genuine rulebook get a
     * compliance note, so both the filled and the empty state are visible.
     */
    private function industryPages(): void
    {
        // name => [excerpt, challenges, compliance notes or null]
        $sectors = [
            'Battery' => [
                'Main, warning, terminal and warranty labels on paper built in-house for electrolyte splash, bonnet heat and high-speed applicators.',
                ['A battery label does not fail slowly. It lifts at a corner, the ink bleeds, or it never gripped properly in the first place — and a return on a battery is never just the label.', [
                    'Electrolyte splash on and around the terminals',
                    'Bonnet temperatures for hours at a time',
                    'High-speed applicators that punish a soft adhesive',
                ]],
                null,
            ],
            'RO & water purifiers' => [
                'Labels that go on wet, stay on wet, and still look new after a year of condensation.',
                ['Purifier bodies are wiped down, dripped on and sweated against. Most label stock is specified dry and tested dry; this one is not.', [
                    'Condensation between the label and a cold surface',
                    'Curved and textured housings',
                    'Detergent wipe-downs in a showroom',
                ]],
                null,
            ],
            'Pharma' => [
                'Cartons and labels where the mandated copy is laid out first and the brand block is fitted around it.',
                ['Pharma artwork is the only category where the regulator writes part of the layout. The job is fitting everything that must be there, at a size that can still be read, without the pack losing its shelf identity.', [
                    'Mandated copy at legible point sizes',
                    'Batch, expiry and serialisation blocks that survive over-printing',
                    'Tamper-evidence that does not fight the die-line',
                ]],
                ['Mandated copy, batch and expiry blocks are placed before the brand block, at sizes agreed with your regulatory team.', 'Artwork changes are versioned and the approved proof is retained, so a reorder prints the approved version and not the newest file on someone\'s desk.'],
            ],
            'Cosmetics' => [
                'Foil, spot UV and small type on curved surfaces — the category where the finish is half the product.',
                ['Cosmetics packaging is read at arm\'s length in a shop and at 20cm in a bathroom. It has to work at both, on a surface that curves away from you.', [
                    'Foil and spot UV registered to fine artwork',
                    'Small type wrapping a tight radius',
                    'Oils and creams on the label face',
                ]],
                null,
            ],
            'Toys' => [
                'Child-safe inks, age marking and retail hanging, on packs built to be handled by children.',
                ['A toy pack is opened by an adult and handled by a child. Everything on it has to be safe in a mouth and strong enough to survive a shelf being rummaged through.', [
                    'Inks and coatings safe for mouth contact',
                    'Age and safety marks that must not be trimmed off',
                    'Hanging tabs that take repeated handling',
                ]],
                ['Inks, coatings and adhesives are specified for mouth contact and confirmed with the supplier for each run.', 'Age grading and safety marks are placed inside the trim tolerance so they cannot be cut away by a die that drifts.'],
            ],
            'Food' => [
                'Food-safe inks and coatings, for packs that go from a cold chain to a counter.',
                ['Food packaging changes temperature more than any other category we print. It is filled warm, chilled, frozen, thawed and then handled — and the label has to be legible at every step.', [
                    'Migration-safe inks and coatings',
                    'Freezer and chiller adhesion',
                    'Condensation on the way back up to room temperature',
                ]],
                ['Inks, varnishes and adhesives are specified as food-safe for the contact type of the pack, direct or indirect.', 'Batch and best-before blocks are laid out for whatever coding equipment you run, so they stay legible after over-printing.'],
            ],
            'Pizza & QSR' => [
                'Grease-resistant print and a flute spec rebuilt for stacking in a delivery bag.',
                ['A QSR box is filled hot, stacked, carried on a bike and opened at a table. By then the print has had twenty minutes of steam and grease against it.', [
                    'Grease and steam against the printed face',
                    'Stacking strength when the board is warm and damp',
                    'Repeat colour across weekly reorders',
                ]],
                null,
            ],
            'Surgical' => [
                'Sterile-pack ready labels and cartons, with the traceability blocks laid out to be read after sterilisation.',
                ['A surgical pack is sterilised after it is labelled. Whatever the label was before that, it has to be legible and still attached afterwards.', [
                    'Adhesion and legibility through the sterilisation cycle',
                    'Lot and traceability blocks that survive the process',
                    'No particulates or loose fibre from the label edge',
                ]],
                ['Lot, reference and sterilisation-method blocks are laid out to remain legible after the cycle your pack goes through.', 'Stock and adhesive are chosen for the sterilisation method and confirmed on a trial run before the first production order.'],
            ],
            'Electrical' => [
                'Warning and rating labels that have to stay readable for the life of the product.',
                ['An electrical rating label is the last thing anyone reads before they work on a product, sometimes years after it was made. It is also the one nobody looks at until it matters.', [
                    'Legibility after years of dust, heat and handling',
                    'Warning marks that must not fade or lift',
                    'Ratings that stay accurate across a product\'s variants',
                ]],
                ['Rating and warning marks are printed at the size and contrast your approvals require, and the approved proof is retained for reorders.'],
            ],
        ];

        foreach ($sectors as $name => [$excerpt, [$intro, $points], $compliance]) {
            $industry = Industry::where('name', $name)->first();

            if (! $industry) {
                continue;
            }

            $industry->fill(array_filter([
                'excerpt' => $industry->excerpt ?: $excerpt,
                'challenges' => $industry->challenges ?: $this->richtext($intro, $points),
                'compliance_notes' => $industry->compliance_notes ?: ($compliance ? $this->richtext(null, $compliance) : null),
            ]))->save();
        }
    }

    /** Trix-shaped HTML: a lead paragraph and a bullet list, the way the editor saves it. */
    private function richtext(?string $intro, array $points): string
    {
        $html = $intro ? '<div>'.e($intro).'</div>' : '';

        return $html.'<ul>'.implode('', array_map(fn ($point) => '<li>'.e($point).'</li>', $points)).'</ul>';
    }

    /**
     * Illustrative blog posts, so /insights and the post template can be seen
     * with something in them. Ours, not the client's.
     */
    private function insights(): void
    {
        foreach (['Materials', 'Packaging', 'Printing'] as $i => $name) {
            PostCategory::firstOrCreate(['name' => $name], ['sort' => $i + 1]);
        }

        // title, category, days ago, excerpt, body paragraphs
        $posts = [
            ['Why your label lifts at one corner', 'Materials', 12,
                'It is almost never the adhesive on its own. It is the adhesive, the surface and the twenty minutes after application.',
                [
                    'A label that lifts at a corner has usually been applied to a surface that was not ready for it — dusty, oily, still warm from a process upstream, or curved more tightly than the stock will take.',
                    'The fix is rarely a stronger adhesive. A stronger adhesive on a bad surface just fails more slowly. What works is matching the stock to the surface and giving the bond the time it needs before the product is stacked.',
                    'If a label is lifting on you, send us one that has failed rather than one from the box. The failed one tells us far more.',
                ]],
            ['Ply is a load decision, not a quality one', 'Packaging', 34,
                'More ply is not better packaging. It is heavier packaging, and you are paying to ship the difference.',
                [
                    'People ask for 7-ply the way they ask for a bigger engine — on the assumption that more is safer. Often it is, but it is also heavier, dearer and takes more space flat.',
                    'The right answer comes from three things: the weight going inside, how high it will be stacked, and how far it travels. Get those and the board picks itself.',
                    'We would rather send you 5-ply that holds than 9-ply you did not need.',
                ]],
            ['When digital beats offset, and when it does not', 'Printing', 61,
                'The crossover is a real number, and it is worth knowing where yours sits.',
                [
                    'Offset spreads the cost of plates and make-ready across the run, so the more you print the less each piece costs. Digital has no plates, so a short run is cheap — but the unit cost stays flat however many you print.',
                    'That means there is a crossover quantity for every job. Below it, digital wins. Above it, offset wins, usually by a lot.',
                    'Because we own both, we have no reason to push you to the wrong side of that line. Ask for the arithmetic when you ask for the quote.',
                ]],
        ];

        foreach ($posts as $i => [$title, $category, $daysAgo, $excerpt, $paragraphs]) {
            Post::firstOrCreate(['title' => $title], [
                'post_category_id' => PostCategory::where('name', $category)->value('id'),
                'excerpt' => $excerpt,
                'body' => implode('', array_map(fn ($line) => '<div>'.e($line).'</div>', $paragraphs)),
                'published_at' => now()->subDays($daysAgo),
            ]);
        }
    }
}
