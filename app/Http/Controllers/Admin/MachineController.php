<?php

namespace App\Http\Controllers\Admin;

use App\Models\Machine;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class MachineController extends ResourceController
{
    protected string $model = Machine::class;

    protected string $slug = 'machines';

    protected string $singular = 'Machine';

    protected string $plural = 'Machine park';

    protected array $search = ['name', 'make', 'model'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('name', 'Name')->required()->help('e.g. Heidelberg Speedmaster SM-74'),
            Field::select('category', 'Category', Machine::CATEGORIES)->required(),
            Field::text('make', 'Make'),
            Field::text('model', 'Model'),
            Field::textarea('description', 'Description'),
            Field::keyvalue('specs', 'Specifications')->help('e.g. Max sheet size → 520 × 740 mm'),
            Field::image('image', 'Photo', 'machines'),
            Field::number('sort', 'Sort order')->default(0),
            Field::toggle('is_published', 'Published')->default(true),
        ];
    }

    protected function columns(): array
    {
        return [['image', '', 'image'], ['name', 'Name'], ['category', 'Category'], ['is_published', 'Published', 'bool'], ['sort', 'Sort']];
    }
}
