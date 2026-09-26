<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\ProductType;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * The portfolio. Filtering is plain query strings against the three pivot
 * tables, rendered on the server, so it works without JavaScript and every
 * filtered view is its own shareable URL.
 *
 * Facets with nothing behind them are not offered: a filter that can only
 * return an empty page is worse than no filter.
 */
class WorkController extends Controller
{
    /** Query key => [relation, model] */
    private const FACETS = [
        'industry' => ['industries', Industry::class],
        'product' => ['productTypes', ProductType::class],
        'tech' => ['technologies', Technology::class],
    ];

    public function index(Request $request): View
    {
        $active = [];

        foreach (array_keys(self::FACETS) as $key) {
            if ($slug = trim((string) $request->query($key))) {
                $active[$key] = $slug;
            }
        }

        return view('work.index', [
            'projects' => $this->filtered($active)
                // Only published sectors: the card falls back to naming them, and
                // a withdrawn sector should not reappear there.
                ->with(['client', 'industries' => fn ($query) => $query->where('is_published', true)])
                ->ordered()
                ->paginate(12)
                ->withQueryString(),
            'facets' => $this->facets($active),
            'active' => $active,
            'total' => Project::published()->count(),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        $project->load([
            'client', 'productTypes', 'technologies',
            'industries' => fn ($query) => $query->where('is_published', true),
        ]);

        return view('work.show', [
            'project' => $project,
            'related' => Project::published()
                ->whereKeyNot($project->getKey())
                ->when($project->industries->isNotEmpty(), fn (Builder $query) => $query->whereHas(
                    'industries',
                    fn (Builder $q) => $q->whereIn('industries.id', $project->industries->modelKeys()),
                ))
                ->ordered()->limit(3)->get(),
        ]);
    }

    /**
     * The qualified slug column for a facet, taken from the model rather than
     * built out of the relation name: `productTypes` lives in `product_types`,
     * and only `industries` and `technologies` are spelled the same both ways.
     */
    private static function slugColumn(string $key): string
    {
        [, $model] = self::FACETS[$key];

        return (new $model)->getTable().'.slug';
    }

    /** @param  array<string, string>  $active */
    private function filtered(array $active): Builder
    {
        $query = Project::published();

        foreach ($active as $key => $slug) {
            [$relation] = self::FACETS[$key];
            $query->whereRelation($relation, self::slugColumn($key), $slug);
        }

        return $query;
    }

    /**
     * Each facet, counted *within the other active filters* — so the number on a
     * chip is what you would actually get by clicking it, and a combination that
     * returns nothing is never offered.
     *
     * The currently selected term is kept even at zero, otherwise a filter that
     * narrows to nothing would remove the control you need to undo it.
     *
     * @param  array<string, string>  $active
     * @return array<string, Collection<int, object>>
     */
    private function facets(array $active): array
    {
        $counted = [];

        foreach (self::FACETS as $key => [$relation, $model]) {
            $counted[$key] = $model::query()
                ->withCount(['projects' => function ($query) use ($active, $key) {
                    $query->where('is_published', true);

                    foreach ($active as $otherKey => $slug) {
                        if ($otherKey === $key) {
                            continue;
                        }

                        [$otherRelation] = self::FACETS[$otherKey];
                        $query->whereRelation($otherRelation, self::slugColumn($otherKey), $slug);
                    }
                }])
                ->orderBy('sort')->orderBy('id')->get()
                // Withdrawn from the site, or a dead end, unless it is the one selected
                ->filter(fn ($term) => ($term->is_published ?? true)
                    && ($term->projects_count > 0 || ($active[$key] ?? null) === $term->slug))
                ->values();
        }

        return $counted;
    }
}
