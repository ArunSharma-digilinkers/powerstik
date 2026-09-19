<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\PostCategory;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PostController extends ResourceController
{
    protected string $model = Post::class;

    protected string $slug = 'posts';

    protected string $singular = 'Post';

    protected string $plural = 'Insights';

    protected array $search = ['title', 'excerpt'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('title', 'Title')->required()->full(),
            Field::slug()->help('Leave empty to generate from the title.'),
            Field::select('post_category_id', 'Category', PostCategory::ordered()->pluck('name', 'id')->all()),
            Field::textarea('excerpt', 'Excerpt')->rules('max:500'),
            Field::image('cover_image', 'Cover image', 'posts'),
            Field::richtext('body', 'Body'),
            Field::datetime('published_at', 'Publish date')->help('Empty = draft. A future date schedules the post.'),
            Field::text('seo_title', 'SEO title'),
            Field::textarea('seo_description', 'SEO description')->rules('max:500'),
        ];
    }

    protected function columns(): array
    {
        return [['cover_image', '', 'image'], ['title', 'Title'], ['category.name', 'Category'], ['published_at', 'Published', 'date']];
    }

    protected function beforeSave(Model $record, Request $request): void
    {
        $record->author_id ??= $request->user()->id;
    }
}
