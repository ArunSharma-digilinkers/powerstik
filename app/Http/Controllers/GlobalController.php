<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ProductType;
use App\Support\ExportMap;
use App\Support\Site;
use Illuminate\View\View;

/**
 * The export page, and the home of the international enquiry form — the
 * `#enquiry` anchor PLAN.md §3 asks for. `/international-enquiry` stays as a
 * direct landing page and posts to the same intake.
 */
class GlobalController extends Controller
{
    private const MEDIA = [
        'export_map' => 'media/home/export-map.webp', // the client's own artwork, shared with home
    ];

    public function __invoke(): View
    {
        $countries = Site::exportCountries();

        return view('global', [
            'countries' => $countries,
            'arcs' => ExportMap::arcs($countries->pluck('name')->all()),
            // Clients we have been given permission to name, that are tied to a market
            'exportClients' => Client::published()->whereNotNull('export_country_id')
                ->with('exportCountry')->ordered()->get(),
            'productTypes' => ProductType::ordered()->pluck('name'),
            'media' => array_map(fn ($path) => is_file(public_path($path)) ? asset($path) : null, self::MEDIA),
        ]);
    }
}
