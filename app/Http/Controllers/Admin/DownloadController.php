<?php

namespace App\Http\Controllers\Admin;

use App\Models\Download;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class DownloadController extends ResourceController
{
    protected string $model = Download::class;

    protected string $slug = 'downloads';

    protected string $singular = 'Download';

    protected string $plural = 'Downloads';

    protected array $search = ['title'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('title', 'Title')->required(),
            Field::select('category', 'Category', Download::CATEGORIES),
            Field::textarea('description', 'Description')->rules('max:500'),
            Field::file('file', 'File', 'downloads')->required()->help('PDF preferred. Max 30 MB.'),
            Field::image('cover_image', 'Cover thumbnail', 'downloads'),
            Field::toggle('requires_email', 'Ask for email before download'),
            Field::toggle('is_published', 'Published')->default(true),
            Field::number('sort', 'Sort order')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['title', 'Title'], ['category', 'Category'], ['requires_email', 'Email gate', 'bool'], ['is_published', 'Published', 'bool']];
    }
}
