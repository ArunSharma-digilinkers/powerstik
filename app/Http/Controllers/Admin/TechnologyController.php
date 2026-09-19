<?php

namespace App\Http\Controllers\Admin;

use App\Models\Technology;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class TechnologyController extends ResourceController
{
    protected string $model = Technology::class;

    protected string $slug = 'technologies';

    protected string $singular = 'Technology';

    protected string $plural = 'Technologies';

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
