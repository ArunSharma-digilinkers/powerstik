<?php

namespace App\Http\Controllers\Admin;

use App\Models\Industry;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class IndustryController extends ResourceController
{
    protected string $model = Industry::class;

    protected string $slug = 'industries';

    protected string $singular = 'Industry';

    protected string $plural = 'Industries';

    protected array $search = ['name'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('name', 'Name')->required(),
            Field::slug(),
            Field::textarea('excerpt', 'Short intro')->rules('max:500')->help('Shown on the industry card and as the page intro.'),
            Field::text('note', 'Card note')->rules('max:120')->help('The technical constraint shown on the home page card, e.g. "Acid & heat resistant".'),
            Field::image('card_image', 'Card photo', 'industries'),
            Field::image('hero_image', 'Page hero photo', 'industries'),
            Field::richtext('challenges', 'Sector challenges'),
            Field::richtext('what_we_supply', 'What Powerstik supplies'),
            Field::richtext('compliance_notes', 'Compliance notes')->help('e.g. pharma legibility, food-safe inks'),
            Field::text('seo_title', 'SEO title'),
            Field::textarea('seo_description', 'SEO description')->rules('max:500'),
            Field::number('sort', 'Sort order')->default(0),
            Field::toggle('is_published', 'Published')->default(true),
        ];
    }

    protected function columns(): array
    {
        return [['card_image', '', 'image'], ['name', 'Name'], ['slug', 'Slug'], ['is_published', 'Published', 'bool'], ['sort', 'Sort']];
    }
}
