<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\ExportCountry;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ClientController extends ResourceController
{
    protected string $model = Client::class;

    protected string $slug = 'clients';

    protected string $singular = 'Client';

    protected string $plural = 'Clients';

    protected array $search = ['name'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('name', 'Name')->required(),
            Field::image('logo', 'Logo', 'clients')->allowSvg()->help('SVG or transparent PNG preferred.'),
            Field::url('website', 'Website'),
            Field::select('export_country_id', 'Export country', ExportCountry::ordered()->pluck('name', 'id')->all())
                ->help('Leave empty for domestic clients.'),
            Field::toggle('show_logo', 'Permission to show publicly'),
            Field::number('sort', 'Sort order')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['logo', '', 'image'], ['name', 'Name'], ['exportCountry.name', 'Export country'], ['show_logo', 'Public', 'bool'], ['sort', 'Sort']];
    }
}
