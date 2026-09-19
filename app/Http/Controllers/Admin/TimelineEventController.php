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
            Field::text('kicker', 'Kicker')->help('Short red label above the title, e.g. "First press".'),
            Field::text('title', 'Title')->required(),
            Field::textarea('body', 'Text'),
            Field::text('meta', 'Footnote')->help('One line under the text, e.g. "Heidelberg SM-74 installed".'),
            Field::image('image', 'Photo', 'timeline')->help('4:3. An archive photo of that year works best.'),
            Field::number('sort', 'Order within the year')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['year', 'Year'], ['title', 'Title']];
    }
}
