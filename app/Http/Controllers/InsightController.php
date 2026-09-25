<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** The blog. Drafts and future-dated posts are not published (Post::scopePublished). */
class InsightController extends Controller
{
    public function index(Request $request): View
    {
        $category = trim((string) $request->query('category')) ?: null;

        return view('insights.index', [
            'posts' => Post::published()
                ->when($category, fn ($query, $slug) => $query->whereRelation('category', 'slug', $slug))
                ->with('category')->ordered()->paginate(9)->withQueryString(),
            // Only categories with something published behind them
            'categories' => PostCategory::query()
                ->withCount(['posts' => fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())])
                ->orderBy('sort')->orderBy('id')->get()
                ->filter(fn (PostCategory $term) => $term->posts_count > 0)
                ->values(),
            'active' => $category,
            'total' => Post::published()->count(),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->published_at && $post->published_at->isPast(), 404);

        $post->load('category', 'author');

        return view('insights.show', [
            'post' => $post,
            'related' => Post::published()->whereKeyNot($post->getKey())
                ->when($post->post_category_id, fn ($query, $id) => $query->where('post_category_id', $id))
                ->with('category')->ordered()->limit(3)->get(),
        ]);
    }
}
