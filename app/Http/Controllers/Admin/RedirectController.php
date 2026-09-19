<?php

namespace App\Http\Controllers\Admin;

use App\Models\Redirect;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RedirectController extends ResourceController
{
    protected string $model = Redirect::class;

    protected string $slug = 'redirects';

    protected string $singular = 'Redirect';

    protected string $plural = 'Redirects';

    protected array $search = ['from_path', 'to_url'];

    protected int $perPage = 50;

    protected function fields(Model $record): array
    {
        return [
            Field::text('from_path', 'Old path')->required()
                ->rules(Rule::unique('redirects', 'from_path')->ignore($record->getKey()))
                ->help('e.g. /products/battery-labels.html'),
            Field::text('to_url', 'Redirect to')->required()->help('New path (/battery-labels) or full URL'),
            Field::select('status_code', 'Type', [301 => '301 Permanent', 302 => '302 Temporary'])->required()->default(301),
        ];
    }

    protected function columns(): array
    {
        return [['from_path', 'From'], ['to_url', 'To'], ['status_code', 'Code'], ['hits', 'Hits'], ['last_hit_at', 'Last hit', 'date']];
    }

    protected function beforeSave(Model $record, Request $request): void
    {
        $record->from_path = Redirect::normalizePath($record->from_path);
    }
}
