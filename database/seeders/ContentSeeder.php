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

        // The descriptions are the home page's machine-park teaser copy, already live.
        // The digital press shipped with no make for a year: the brochure credited Canon
        // but the only picture we held was a Xerox Color 175. Arun photographed the press
        // on 2026-10-17 and it is a Canon imagePRESS V900, M/C no. 17, so the row is
        // renamed in place rather than added beside the old one.
        $machines = [
            ['Heidelberg Speedmaster SM-74', 'offset', 'Heidelberg', 'SM-74', 'Five-colour, for cartons and sheet labels.'],
            ['Heidelberg Speedmaster CD-102', 'offset', 'Heidelberg', 'CD-102', 'Large format, for long runs and heavy stock.'],
            ['Canon imagePRESS V900', 'digital', 'Canon', 'imagePRESS V900', 'Short runs, samples and variable data, without a plate or a minimum.'],
            ['Mark Andy E5', 'flexo', 'Mark Andy', 'E5', 'Roll label press — applicator-ready reels, core size and winding direction to spec.'],
            ['Corrugation line', 'corrugation', null, null, 'Print, slot and die-cut in line: mono cartons through 3, 5, 7 and 9-ply, in-house.'],
            ['Rhyguan label converter', 'finishing', 'Rhyguan', null, 'Die-cutting and rewinding for roll labels, with the web on a screen while it runs.'],
            ['Flat-bed die-cutter', 'finishing', null, null, 'Shapes a printed label between an unwind and a rewind, for dies the roll press does not carry.'],
            ['HPM 92 guillotine', 'finishing', 'HPM', '92', 'Trims the sheet square before it is printed and cuts the job down after it, to a programmed stack.'],
        ];
        Machine::where('name', 'Digital press')->whereNull('make')
            ->update(['name' => 'Canon imagePRESS V900', 'make' => 'Canon', 'model' => 'imagePRESS V900']);

        foreach ($machines as $i => $m) {
            $machine = Machine::firstOrCreate(['name' => $m[0]], [
                'category' => $m[1], 'make' => $m[2], 'model' => $m[3], 'sort' => $i + 1,
            ]);

            // Backfill the description onto rows seeded before it existed, without
            // overwriting anything an editor has since written. (Note: calling
            // whereNull() on the instance would query the whole table, not this row.)
            if (blank($machine->description)) {
                $machine->update(['description' => $m[4]]);
            }
        }
        $this->attachMachinePhotos();

        // name, ISO, lat, lng (approximate centroid, used for the export map), card note.
        // The list is the brochure's "Powering Brands Worldwide" map plus Bangladesh and the
        // USA, which the client confirmed separately. The per-country notes are still ours.
        $countries = [
            ['Nepal', 'NP', 28.39, 84.12, 'Battery labels'], ['Bangladesh', 'BD', 23.68, 90.36, 'Labels & cartons'],
            ['Afghanistan', 'AF', 33.94, 67.71, 'Battery labels'], ['Uganda', 'UG', 1.37, 32.29, 'Battery labels'],
            ['USA', 'US', 37.09, -95.71, 'Speciality print'], ['Fiji', 'FJ', -17.71, 178.07, 'Labels'],
            ['Russia', 'RU', 61.52, 105.32, 'Battery labels'], ['Algeria', 'DZ', 28.03, 1.66, 'Battery labels'],
            ['UAE', 'AE', 23.42, 53.85, 'Battery labels'], ['Nigeria', 'NG', 9.08, 8.68, 'Battery labels'],
            ['Zimbabwe', 'ZW', -19.02, 29.15, 'Battery labels'],
        ];
        foreach ($countries as $i => [$name, $iso, $lat, $lng, $note]) {
            ExportCountry::firstOrCreate(['iso2' => $iso], ['name' => $name, 'lat' => $lat, 'lng' => $lng, 'sort' => $i + 1]);
            ExportCountry::where('iso2', $iso)->whereNull('note')->update(['note' => $note]);
        }

        // The brochure's "Our Major Clientele" row, plus the names on the logo sheet
        // the client supplied (media-inbox/clientele.png). show_logo is on because
        // Design India already prints these logos in its own brochure; confirmed by
        // Arun on 2026-09-25. Turn one off in Admin → Clients if a client objects.
        $clients = ['Livguard', 'Eastman', 'Unique Energos', 'Tata Green', 'Amaron',
            'Livfast', 'Uno Minda', 'Solance', 'Su-Kam', 'UTL',
            'Exide', 'Gold Star', 'Pacific', 'Green Energy Plus', 'Noor Batt'];
        foreach ($clients as $i => $name) {
            Client::firstOrCreate(['name' => $name], ['sort' => $i + 1, 'show_logo' => true]);
        }
        $this->attachClientLogos();

        // The story milestones, as the client gave them on 2026-10-17. Matched on the
        // year alone, so a correction lands on the row that is already there instead of
        // adding a second tab for the same year.
        //
        // The paragraphs are ours, not the client's, and they were written on 2026-10-17
        // at Arun's request. Every one of them is built only out of facts the site already
        // states elsewhere — the brochure's founding, the export count, the roll-label
        // specs, the ply range, the Rai address in the footer. Nothing here invents an
        // event, a client or a machine. If you add a milestone, hold that line, and let
        // an editor's own copy win: a paragraph is only ever filled when it is empty.
        $milestones = [
            [2002, 'The beginning', 'Design India opens in Delhi', 'Design India founded · Delhi',
                'Amit and Sumit Sharma opened Design India as a design consultancy in Delhi. Logos, labels and packaging artwork were drawn here and printed by somebody else.'],
            [2007, 'First press', 'Printing comes in-house', null,
                'The first press arrived and the work stopped going out to be printed. Design and print have been under one roof ever since.'],
            [2019, 'New office', 'The team moves into a new office', null,
                'The studio moved into a room of its own in Delhi — open desks, glass and daylight. The photograph is from the week it opened.'],
            [2021, 'Export', 'The first consignment leaves India', 'Exports begin · now 11 countries',
                'The first consignment left India. The work reaches eleven markets today, and the bench that packs a job files its export papers too.'],
            [2022, 'Roll labels', 'Roll labels join the sheet line', null,
                'Roll labels joined the sheet line, so a client running an automatic applicator could be supplied without changing vendor. Core size and winding direction to spec, reel after reel.'],
            [2024, 'New plant', 'A new plant at Rai', 'Rai · Haryana',
                'After twenty-two years in Delhi, the floor moved to Rai Industrial Estate in Sonipat. Design, print, finishing and dispatch now sit on one site.'],
            [2025, 'Corrugation', 'Corrugation comes in-house', '3, 5, 7 and 9-ply',
                'Corrugation came in-house and the box stopped being bought in. Mono cartons through 3, 5, 7 and 9-ply, printed, slotted and die-cut on the same floor as the label that goes on them.'],
        ];
        foreach ($milestones as [$year, $kicker, $title, $meta, $body]) {
            $event = TimelineEvent::firstOrCreate(['year' => $year], compact('kicker', 'title', 'meta', 'body'));

            // The brief shipped a one-line stub for 2002 before any of this was confirmed;
            // replace that outright. Otherwise only fill a paragraph that is still empty —
            // an editor's own words are never overwritten.
            if ($event->body === 'Powerstik began as a small design studio.') {
                $event->update(compact('kicker', 'title', 'meta', 'body'));
            } elseif (blank($event->body)) {
                $event->update(compact('body'));
            }
        }
        // Arun corrected the founding on 2026-10-17: Design India started in **Delhi** and
        // the company stayed there until the move to Rai in 2024. Rows seeded before that
        // say Haryana, and the loop above will not touch them because their paragraph is no
        // longer empty. Rewrite the superseded wording, and only that wording, so an
        // editor's own copy is still safe. Drop this block once the server has run it.
        $bodies = collect($milestones)->keyBy(0)->map(fn (array $m) => $m[4]);

        TimelineEvent::where('year', 2002)->where('title', 'Design India opens in Haryana')
            ->update(['title' => 'Design India opens in Delhi', 'meta' => 'Design India founded · Delhi']);

        foreach ([
            2002 => 'Amit and Sumit Sharma opened Design India as a design consultancy in Haryana. Logos, labels and packaging artwork were drawn here and printed by somebody else.',
            2019 => 'The studio moved into a room of its own — open desks, glass and daylight. The photograph is from the week it opened.',
            2024 => 'The floor moved to Rai Industrial Estate in Sonipat. Design, print, finishing and dispatch sit on one site.',
        ] as $year => $superseded) {
            TimelineEvent::where('year', $year)->where('body', $superseded)->update(['body' => $bodies[$year]]);
        }

        $this->attachTimelinePhotos();

        // Roles and bios are the client's own words, from the brochure's "Two minds / One spark!".
        $leaders = [
            'Amit Sharma' => [
                'Founder & head of Design India', 'Operations & backend',
                '<div>Founder and head of Design India. His innovative ideas and focus on improving design and quality have helped Powerstik achieve the No.1 position in the market over 25 years. His dedication and clear vision have driven the company to excellence, making it a leader in the industry.</div>',
            ],
            'Sumit Sharma' => [
                'Co-founder · Marketing & Design', 'Marketing & clients',
                '<div>Co-founder of Design India, leading the Marketing and Designing Division and focused on delivering unique, client-tailored solutions. His strategic management ensures efficient workflow and product delivery, with a keen market understanding and a passion for design. He continuously refines product quality and creates innovative solutions for clients.</div>',
            ],
        ];
        $sort = 0;
        foreach ($leaders as $name => [$role, $placeholderRole, $bio]) {
            TeamMember::firstOrCreate(['name' => $name], [
                'role' => $role, 'department' => 'leadership', 'is_leadership' => true, 'sort' => ++$sort,
            ]);
            // Rows seeded before the brochure arrived carry a role we invented; replace only those.
            TeamMember::where('name', $name)->where('role', $placeholderRole)->update(['role' => $role]);
            TeamMember::where('name', $name)->whereNull('bio')->update(['bio' => $bio]);
        }
        $this->attachPortraits();

        $faqs = [
            ['What is the minimum order quantity?', '<div>From 50 units, up to any volume, in sheet or roll form.</div>', 'Orders'],
            ['How quickly do you dispatch?', '<div>Within 2–5 days of artwork approval, including bulk orders.</div>', 'Orders'],
            ['Do you export?', '<div>Yes. We currently export to Nepal, Bangladesh, Afghanistan, UAE, Uganda, Nigeria, Zimbabwe, the USA, Fiji, Russia and Algeria.</div>', 'Export'],
        ];
        foreach ($faqs as $i => [$q, $a, $cat]) {
            Faq::firstOrCreate(['question' => $q], ['answer' => $a, 'category' => $cat, 'sort' => $i + 1]);
        }
        // The export list is a fact, so it is corrected in place when countries are added.
        Faq::where('question', 'Do you export?')->update(['answer' => $faqs[2][1]]);

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

    /** Archive photographs for the story timeline (seeders/media/timeline/{year}.jpg). */
    private function attachTimelinePhotos(): void
    {
        foreach (glob(database_path('seeders/media/timeline/*.jpg')) as $file) {
            $event = TimelineEvent::where('year', (int) pathinfo($file, PATHINFO_FILENAME))->whereNull('image')->first();

            $event?->update(['image' => ImageStore::store(new UploadedFile($file, basename($file), 'image/jpeg', null, true), 'timeline')]);
        }
    }

    /**
     * Machine photographs (seeders/media/machines/{name-slug}.jpg), only where no photo
     * is set yet. Uploads made in Admin → Machines land in public/uploads, which is
     * git-ignored, so this is the only route a machine photograph has to the server.
     */
    private function attachMachinePhotos(): void
    {
        $machines = Machine::all()->keyBy(fn (Machine $machine) => Str::slug($machine->name));

        foreach (glob(database_path('seeders/media/machines/*.jpg')) as $file) {
            $machine = $machines->get(pathinfo($file, PATHINFO_FILENAME));

            if (! $machine || filled($machine->image)) {
                continue;
            }

            $machine->update([
                'image' => ImageStore::store(new UploadedFile($file, basename($file), 'image/jpeg', null, true), 'machines'),
            ]);
        }
    }

    /**
     * Industry images prepared from the client's media, named by slug:
     *   seeders/media/industries/{slug}.jpg       → card_image (4:3, the home grid)
     *   seeders/media/industries/hero/{slug}.jpg  → hero_image (16:9, the detail page)
     * Only fills fields that are still empty, so uploads made in the admin win.
     */
    /**
     * Client logos, cut from the sheet the client supplied and kept as transparent
     * PNGs at seeders/media/clients/{name-slug}.png. Only fills an empty logo, and
     * never touches show_logo, which is the client's own permission flag.
     */
    private function attachClientLogos(): void
    {
        $clients = Client::all()->keyBy(fn (Client $client) => Str::slug($client->name));

        foreach (glob(database_path('seeders/media/clients/*.png')) as $file) {
            $client = $clients->get(pathinfo($file, PATHINFO_FILENAME));

            if (! $client || filled($client->logo)) {
                continue;
            }

            $client->update([
                'logo' => ImageStore::store(new UploadedFile($file, basename($file), 'image/png', null, true), 'clients'),
            ]);
        }
    }

    /**
     * Industry photography. A card is a photograph and ships as JPG; a hero is a
     * cutout that sits on the ink, so it ships as PNG and keeps its transparency.
     */
    private function attachIndustryCards(): void
    {
        foreach (['' => 'card_image', 'hero/' => 'hero_image'] as $dir => $field) {
            foreach (glob(database_path("seeders/media/industries/{$dir}*.{jpg,png}"), GLOB_BRACE) as $file) {
                $industry = Industry::where('slug', pathinfo($file, PATHINFO_FILENAME))->whereNull($field)->first();
                $mime = pathinfo($file, PATHINFO_EXTENSION) === 'png' ? 'image/png' : 'image/jpeg';

                $industry?->update([$field => ImageStore::store(new UploadedFile($file, basename($file), $mime, null, true), 'industries')]);
            }
        }
    }
}
