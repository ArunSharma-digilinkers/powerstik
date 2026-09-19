<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExportCountry;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ExportCountryController extends ResourceController
{
    protected string $model = ExportCountry::class;

    protected string $slug = 'export-countries';

    protected string $singular = 'Export country';

    protected string $plural = 'Export countries';

    protected array $search = ['name', 'iso2'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('name', 'Country')->required(),
            Field::text('iso2', 'ISO code')->required()->rules('size:2', 'alpha')->help('Two letters, e.g. NP'),
            Field::text('lat', 'Latitude')->rules('numeric', 'between:-90,90')->help('Map pin position'),
            Field::text('lng', 'Longitude')->rules('numeric', 'between:-180,180'),
            Field::text('note', 'Card note')->rules('max:120')->help('What we ship there, e.g. "Battery labels". Shown on the home page.'),
            Field::textarea('blurb', 'Longer description')->rules('max:500')->help('For the Global page.'),
            Field::number('sort', 'Sort order')->default(0),
            Field::toggle('is_published', 'Show on site')->default(true),
        ];
    }

    protected function columns(): array
    {
        return [['name', 'Country'], ['iso2', 'ISO'], ['is_published', 'Shown', 'bool'], ['sort', 'Sort']];
    }
}
