<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\Machine;
use App\Support\Site;
use Illuminate\View\View;

/**
 * The four disciplines and the machine park. Unlike About, these are real
 * sub-pages rather than anchors, because the home page's "What we do" grid and
 * the footer have linked these five URLs since launch.
 *
 * The narrative copy is fixed in Blade (PLAN.md §4) — there is no `capabilities`
 * table. Only the machine park is DB-driven, from Admin → Machine park.
 */
class CapabilityController extends Controller
{
    /**
     * Shared spine per discipline: hero, fact strip and the deliverables list.
     * The part that makes each page its own lives in capabilities/blocks/{slug}.
     */
    public const DISCIPLINES = [
        'design-branding' => [
            'nav' => 'Design & branding',
            'kicker' => 'The studio that started it all',
            'title' => 'It starts at a design table.',
            'intro' => 'Powerstik grew out of a design studio, not a press that later hired designers. Every job still starts where the first one did — with someone drawing it.',
            'facts' => [
                ['In-house', 'No freelance handoff'],
                ['3D mockup', 'Before a plate is made'],
                ['Dielines', 'Drawn to your filling line'],
                ['Free', 'Artwork check with any quote'],
            ],
            'deliverables' => [
                'Logo and brand marks, or a rework of the ones you have',
                'Packaging and label design, from the brief or from a reference',
                'Dielines drawn to your product, your filler and your carton',
                'A 3D mockup you can turn around before anything is committed',
                'Print-ready artwork, separated and trapped for the press it will run on',
                'Retained approved files, so a reorder prints the approved version',
            ],
        ],
        'label-printing' => [
            'nav' => 'Label printing',
            'kicker' => 'Sheet and roll',
            'title' => 'However your line takes them.',
            'intro' => 'Hand-applied or on an applicator, the printing is the same job. What changes is how the labels are cut, counted and wound — and getting that wrong costs more than the labels.',
            'facts' => [
                ['Sheet or roll', 'Cut and counted, or wound'],
                ['Offset · digital · flexo', 'All three in-house'],
                ['{moq}', 'Unit minimum'],
                ['{dispatch}', 'Dispatch after approval'],
            ],
            'deliverables' => [
                'Sheet labels, cut to size, counted and banded',
                'Roll labels wound to your applicator — core, direction, count and diameter',
                'Battery labels on our own paper (they have a page of their own)',
                'Variable data: serials, batch codes, dates',
                'Lamination, spot UV, foil, embossing and die-cut shapes',
                'A retained sample the next run is matched against',
            ],
        ],
        'corrugated-packaging' => [
            'nav' => 'Corrugated packaging',
            'kicker' => 'Mono carton to 9-ply',
            'title' => 'The box has to hold.',
            'intro' => 'A printed box that fails in a warehouse is worse than a plain one that holds. We pick the flute and the ply for the stack and the journey first, and print it second.',
            'facts' => [
                ['To 9-ply', 'Mono carton upward'],
                ['Print · slot · die-cut', 'In line, one pass'],
                ['Flute selection', 'Chosen for the stack'],
                ['{dispatch}', 'Dispatch after approval'],
            ],
            'deliverables' => [
                'Mono cartons, printed and die-cut',
                'Corrugated boxes in 3, 5, 7 and 9-ply',
                'Box styles drawn to your product, not to a catalogue size',
                'Flute and ply chosen for the stack height and the journey',
                'Printed outers that match the label inside them',
                'Inserts, partitions and pads where the product needs them',
            ],
        ],
        'printing-technologies' => [
            'nav' => 'Print technologies',
            'kicker' => 'Offset, digital, flexo',
            'title' => 'The run picks the press.',
            'intro' => 'We own all three, so nothing is steered toward the machine that happens to be free. Here is how we actually choose — and when we tell you the cheaper answer.',
            'facts' => [
                ['Offset', 'Long runs, lowest unit cost'],
                ['Digital', 'Short runs, no plate'],
                ['Flexo', 'Roll labels, inline die-cut'],
                ['One floor', 'No job subcontracted'],
            ],
            'deliverables' => [
                'A recommendation before you ask, with the reason',
                'Digital proofs on the stock the job will run on',
                'Colour read against the approved proof through the run',
                'The same press for the reorder, so the colour matches',
                'A short digital run to prove a design before a long offset one',
                'Honest arithmetic when a smaller quantity is the better buy',
            ],
        ],
    ];

    public function index(): View
    {
        return view('capabilities.index', [
            'disciplines' => self::DISCIPLINES,
            'machines' => Machine::published()->ordered()->get(),
            'industries' => Industry::published()->ordered()->get(),
        ]);
    }

    public function show(string $capability): View
    {
        abort_unless(isset(self::DISCIPLINES[$capability]), 404);

        return view('capabilities.show', [
            'slug' => $capability,
            'meta' => $this->resolve(self::DISCIPLINES[$capability]),
            'others' => array_diff_key(self::DISCIPLINES, [$capability => null]),
        ]);
    }

    public function machines(): View
    {
        return view('capabilities.machine-park', [
            'machines' => Machine::published()->ordered()->get()->groupBy('category'),
            'others' => self::DISCIPLINES,
        ]);
    }

    /**
     * Settings are read at request time, not baked into the constant.
     *
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    private function resolve(array $meta): array
    {
        $values = [
            '{moq}' => 'From '.preg_replace('/\D/', '', (string) Site::setting('moq', '50')),
            '{dispatch}' => trim(preg_replace('/\s*days?$/i', '', (string) Site::setting('dispatch_days', '2–5'))).' days',
        ];

        $meta['facts'] = array_map(
            fn (array $fact) => [strtr($fact[0], $values), $fact[1]],
            $meta['facts'],
        );

        return $meta;
    }
}
