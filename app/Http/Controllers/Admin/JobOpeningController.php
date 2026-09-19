<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobOpening;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class JobOpeningController extends ResourceController
{
    protected string $model = JobOpening::class;

    protected string $slug = 'jobs';

    protected string $singular = 'Job opening';

    protected string $plural = 'Careers';

    protected array $search = ['title', 'department'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('title', 'Job title')->required(),
            Field::slug()->help('Leave empty to generate from the title.'),
            Field::text('department', 'Department'),
            Field::text('location', 'Location'),
            Field::select('employment_type', 'Type', JobOpening::EMPLOYMENT_TYPES),
            Field::richtext('description', 'Description'),
            Field::toggle('is_open', 'Accepting applications')->default(true),
            Field::number('sort', 'Sort order')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['title', 'Title'], ['department', 'Department'], ['location', 'Location'], ['is_open', 'Open', 'bool']];
    }
}
