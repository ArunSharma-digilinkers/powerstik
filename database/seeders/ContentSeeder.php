<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ExportCountry;
use App\Models\Faq;
use App\Models\GlossaryTerm;
use App\Models\Industry;
use App\Models\Machine;
use App\Models\ProductType;
use App\Models\TeamMember;
use App\Models\Technology;
use App\Models\TimelineEvent;
use App\Support\ImageStore;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Starter content taken from Powerstik_Website_Architecture.docx. Only facts
 * stated in the brief; everything else is left for the team to fill in.
 * Idempotent: existing rows (matched by name) are never overwritten.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // name => card note (the constraint each sector brings, from the home page design)
        $industries = [
            'Battery' => 'Acid & heat resistant', 'RO & water purifiers' => 'Wet-surface adhesion',
            'Pharma' => 'Legibility compliance', 'Cosmetics' => 'Foil & spot UV', 'Toys' => 'Child-safe inks',
            'Food' => 'Food-safe inks', 'Pizza & QSR' => 'Grease resistance', 'Surgical' => 'Sterile-pack ready',
            'Electrical' => 'Warning & rating labels',
        ];
        foreach (array_keys($industries) as $i => $name) {
            Industry::firstOrCreate(['name' => $name], ['sort' => $i + 1]);
            Industry::where('name', $name)->whereNull('note')->update(['note' => $industries[$name]]);
        }
        $this->attachIndustryCards();

        foreach (['Offset', 'Digital', 'Flexo'] as $i => $name) {
            Technology::firstOrCreate(['name' => $name], ['sort' => $i + 1]);
        }

        $productTypes = ['Sheet labels', 'Roll labels', 'Battery labels', 'Mono cartons', 'Corrugated boxes', 'Branding & design'];
        foreach ($productTypes as $i => $name) {
            ProductType::firstOrCreate(['name' => $name], ['sort' => $i + 1]);
        }

        $machines = [
            ['Heidelberg Speedmaster SM-74', 'offset', 'Heidelberg', 'SM-74'],
            ['Heidelberg Speedmaster CD-102', 'offset', 'Heidelberg', 'CD-102'],
            ['Canon digital press', 'digital', 'Canon', null],
            ['Mark Andy E5', 'flexo', 'Mark Andy', 'E5'],
            ['Corrugation line', 'corrugation', null, null, 'Full line from mono cartons to 9-ply boxes.'],
        ];
        foreach ($machines as $i => $m) {
            Machine::firstOrCreate(['name' => $m[0]], [
                'category' => $m[1], 'make' => $m[2], 'model' => $m[3], 'description' => $m[4] ?? null, 'sort' => $i + 1,
            ]);
        }

        // name, ISO, lat, lng (approximate centroid, used for the export map), card note
        $countries = [
            ['Nepal', 'NP', 28.39, 84.12, 'Battery labels'], ['Bangladesh', 'BD', 23.68, 90.36, 'Labels & cartons'],
            ['Afghanistan', 'AF', 33.94, 67.71, 'Battery labels'], ['Uganda', 'UG', 1.37, 32.29, 'Battery labels'],
            ['USA', 'US', 37.09, -95.71, 'Speciality print'], ['Fiji', 'FJ', -17.71, 178.07, 'Labels'],
            ['Russia', 'RU', 61.52, 105.32, 'Battery labels'], ['Algeria', 'DZ', 28.03, 1.66, 'Battery labels'],
        ];
        foreach ($countries as $i => [$name, $iso, $lat, $lng, $note]) {
            ExportCountry::firstOrCreate(['iso2' => $iso], ['name' => $name, 'lat' => $lat, 'lng' => $lng, 'sort' => $i + 1]);
            ExportCountry::where('iso2', $iso)->whereNull('note')->update(['note' => $note]);
        }

        // show_logo stays off until each client's permission is confirmed (brief §11).
        foreach (['Livguard', 'Eastman', 'Unique Energos', 'Tata Green', 'Amaron'] as $i => $name) {
            Client::firstOrCreate(['name' => $name], ['sort' => $i + 1, 'show_logo' => false]);
        }

        TimelineEvent::firstOrCreate(['year' => 2002, 'title' => 'Founded as a design setup'], [
            'kicker' => 'The beginning', 'body' => 'Powerstik began as a small design studio.', 'meta' => 'Design India founded · Haryana',
        ]);

        TeamMember::firstOrCreate(['name' => 'Amit Sharma'], [
            'role' => 'Operations & backend', 'department' => 'leadership', 'is_leadership' => true, 'sort' => 1,
        ]);
        TeamMember::firstOrCreate(['name' => 'Sumit Sharma'], [
            'role' => 'Marketing & clients', 'department' => 'leadership', 'is_leadership' => true, 'sort' => 2,
        ]);
        $this->attachPortraits();

        $faqs = [
            ['What is the minimum order quantity?', '<div>From 50 units, up to any volume, in sheet or roll form.</div>', 'Orders'],
            ['How quickly do you dispatch?', '<div>Within 2–5 days of artwork approval, including bulk orders.</div>', 'Orders'],
            ['Do you export?', '<div>Yes. We currently export to Nepal, Bangladesh, Afghanistan, Uganda, the USA, Fiji, Russia and Algeria.</div>', 'Export'],
        ];
        foreach ($faqs as $i => [$q, $a, $cat]) {
            Faq::firstOrCreate(['question' => $q], ['answer' => $a, 'category' => $cat, 'sort' => $i + 1]);
        }

        $glossary = [
            ['Ply', 'The number of layers in a corrugated board. 3-ply has one fluted layer between two liners; 5, 7 and 9-ply add more flute and liner layers for strength.'],
            ['GSM', 'Grams per square metre: the weight, and roughly the thickness, of paper or board.'],
            ['BOPP', 'Biaxially oriented polypropylene: a strong, moisture-resistant plastic film used for labels and lamination.'],
            ['Lamination', 'A thin film bonded over a printed surface to protect it and give a gloss or matt finish.'],
            ['Flute', 'The wavy middle layer of corrugated board that gives it rigidity and cushioning.'],
            ['Mono carton', 'A single-layer folding carton made from paperboard, used for retail product packaging.'],
        ];
        foreach ($glossary as [$term, $def]) {
            GlossaryTerm::firstOrCreate(['term' => $term], ['definition' => "<div>{$def}</div>"]);
        }
    }

    /** Leadership portraits (seeders/media/team/{name-slug}.jpg), only where no photo is set yet. */
    private function attachPortraits(): void
    {
        foreach (glob(database_path('seeders/media/team/*.jpg')) as $file) {
            $member = TeamMember::whereNull('photo')->get()
                ->first(fn (TeamMember $m) => Str::slug($m->name) === pathinfo($file, PATHINFO_FILENAME));

            $member?->update(['photo' => ImageStore::store(new UploadedFile($file, basename($file), 'image/jpeg', null, true), 'team')]);
        }
    }

    /**
     * Card images prepared from the client's media (4:3, in seeders/media/industries/{slug}.jpg).
     * Only fills industries that have no card image yet, so uploads made in the admin win.
     */
    private function attachIndustryCards(): void
    {
        foreach (glob(database_path('seeders/media/industries/*.jpg')) as $file) {
            $industry = Industry::where('slug', pathinfo($file, PATHINFO_FILENAME))->whereNull('card_image')->first();

            if ($industry) {
                $industry->update(['card_image' => ImageStore::store(new UploadedFile($file, basename($file), 'image/jpeg', null, true), 'industries')]);
            }
        }
    }
}
