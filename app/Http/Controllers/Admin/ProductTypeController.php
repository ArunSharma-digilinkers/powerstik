<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductType;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ProductTypeController extends ResourceController
{
    protected string $model = ProductType::class;

    protected string $slug = 'product-types';

    protected string $singular = 'Product type';

    protected string $plural = 'Product types';

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
