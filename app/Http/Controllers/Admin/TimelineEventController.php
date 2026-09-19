<?php

namespace App\Http\Controllers\Admin;

use App\Models\TimelineEvent;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class TimelineEventController extends ResourceController
{
    protected string $model = TimelineEvent::class;

    protected string $slug = 'timeline';

    protected string $singular = 'Timeline event';

    protected string $plural = 'Our story timeline';

    protected array $search = ['title'];

    protected function fields(Model $record): array
    {
        return [
            Field::number('year', 'Year')->required()->rules('between:1990,2100'),
            Field::text('title', 'Title')->required(),
            Field::textarea('body', 'Text'),
            Field::image('image', 'Photo', 'timeline'),
            Field::number('sort', 'Order within the year')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['year', 'Year'], ['title', 'Title']];
    }
}
