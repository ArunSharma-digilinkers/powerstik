<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Faq;
use App\Models\GlossaryTerm;
use App\Models\Post;
use Illuminate\View\View;

/** The resources hub, the FAQs and the material guide. Downloads and insights have their own controllers. */
class ResourcesController extends Controller
{
    public function index(): View
    {
        return view('resources.index', [
            'counts' => [
                'downloads' => Download::published()->whereNotNull('file')->count(),
                'materials' => GlossaryTerm::count(),
                'faqs' => Faq::published()->count(),
                'insights' => Post::published()->count(),
            ],
        ]);
    }

    public function faqs(): View
    {
        // Grouped by category, in the editor's sort order, with uncategorised last
        $faqs = Faq::published()->ordered()->get()
            ->groupBy(fn (Faq $faq) => $faq->category ?: 'Everything else');

        return view('resources.faqs', ['groups' => $faqs]);
    }

    public function materialGuide(): View
    {
        $terms = GlossaryTerm::orderBy('term')->get();

        return view('resources.material-guide', [
            'terms' => $terms,
            // Only the letters that actually have a term behind them
            'letters' => $terms->map(fn (GlossaryTerm $term) => strtoupper($term->term[0] ?? ''))->unique()->values(),
        ]);
    }
}
