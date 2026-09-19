<?php

namespace App\Http\Controllers\Admin;

use App\Models\PostCategory;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class PostCategoryController extends ResourceController
{
    protected string $model = PostCategory::class;

    protected string $slug = 'post-categories';

    protected string $singular = 'Blog category';

    protected string $plural = 'Blog categories';

    protected array $search = ['name'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('name', 'Name')->required(),
            Field::slug(),
            Field::number('sort', 'Sort order')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['name', 'Name'], ['slug', 'Slug'], ['sort', 'Sort']];
    }
}
