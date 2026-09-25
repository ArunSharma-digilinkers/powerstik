<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Project;
use App\Support\Site;
use Illuminate\View\View;

/**
 * The flagship page. One scrolling page with anchors (#paper, #types, #formats,
 * #packaging, #clients, #sample) rather than the four sub-pages in the brief's
 * sitemap, the same way About is built.
 *
 * Everything asserted here is either the client's own words from the brochure or
 * the acid-and-heat protocol Arun confirmed on 2026-09-24. The paper's brand name
 * and its full data sheet are still open items (PLAN.md §7.3), so neither appears.
 */
class BatteryLabelsController extends Controller
{
    /**
     * Fixed page photography. Drop optimised files at these paths under public/
     * and they appear; until then the tinted wells show.
     */
    private const MEDIA = [
        'battery' => 'media/home/battery.webp',      // shared with the home flagship
        'acid_test' => 'media/home/acid-test.webp',  // shared with the home proof band
        'sheet' => 'media/battery-labels/sheet.webp',
        'roll' => 'media/battery-labels/roll.webp',
        'packaging' => 'media/battery-labels/packaging.webp',
    ];

    public function __invoke(): View
    {
        $battery = Industry::where('slug', 'battery')->first();

        return view('battery-labels', [
            'clients' => Client::ordered()->get(),
            'countries' => Site::exportCountries(),
            'cases' => $battery
                ? Project::published()->whereRelation('industries', 'industries.id', $battery->id)->with('client')->ordered()->limit(3)->get()
                : collect(),
            'media' => array_map(fn ($path) => is_file(public_path($path)) ? asset($path) : null, self::MEDIA),
        ]);
    }
}
