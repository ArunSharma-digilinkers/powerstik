<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\ProductType;
use App\Models\Project;
use App\Support\Site;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * The sector pages. One shared template over every published industry — the
 * main SEO asset in PLAN.md §3, and the only part of the site where the page
 * count grows without a deploy.
 *
 * Each sector's own copy (excerpt, challenges, what we supply, compliance) is
 * edited in Admin → Industries. Every one of those blocks stays hidden while
 * its field is empty, so a half-filled sector never ships a heading with
 * nothing under it.
 */
class IndustryController extends Controller
{
    public function index(): View
    {
        return view('industries.index', [
            'industries' => Industry::published()->ordered()->get(),
            'countries' => Site::exportCountries(),
        ]);
    }

    public function show(Industry $industry): View
    {
        abort_unless($industry->is_published, 404);

        $others = Industry::published()->ordered()->whereKeyNot($industry->getKey())->get();

        return view('industries.show', [
            'industry' => $industry,
            // The supply list is the generic one, so drop any product type named
            // after a different sector — "Battery labels" does not belong on the
            // pharma page, but it does belong on the battery one.
            'supplies' => ProductType::ordered()->pluck('name')->reject(
                fn (string $product) => $others->contains(
                    fn (Industry $other) => str_contains(Str::lower($product), Str::lower($other->name)),
                ),
            )->values(),
            'cases' => Project::published()
                ->whereRelation('industries', 'industries.id', $industry->id)
                ->with('client')->ordered()->limit(3)->get(),
            'others' => $others,
        ]);
    }
}
