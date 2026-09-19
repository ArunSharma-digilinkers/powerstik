<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\Industry;
use App\Models\ProductType;
use App\Models\Project;
use App\Models\Technology;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ProjectController extends ResourceController
{
    protected string $model = Project::class;

    protected string $slug = 'projects';

    protected string $singular = 'Project';

    protected string $plural = 'Our work';

    protected array $search = ['title', 'summary'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('title', 'Title')->required(),
            Field::slug()->help('Leave empty to generate from the title.'),
            Field::select('client_id', 'Client', Client::ordered()->pluck('name', 'id')->all()),
            Field::textarea('summary', 'Summary')->rules('max:500'),
            Field::image('cover_image', 'Cover image', 'projects'),
            Field::gallery('gallery', 'Gallery', 'projects'),
            Field::checkboxes('industries', 'Industries', Industry::ordered()->pluck('name', 'id')->all()),
            Field::checkboxes('productTypes', 'Product types', ProductType::ordered()->pluck('name', 'id')->all()),
            Field::checkboxes('technologies', 'Technologies', Technology::ordered()->pluck('name', 'id')->all()),
            Field::toggle('is_case_study', 'This is a full case study')->help('Shows the Problem → What we did → Result sections.'),
            Field::richtext('problem', 'Problem'),
            Field::richtext('solution', 'What we did'),
            Field::richtext('result', 'Result'),
            Field::toggle('is_featured', 'Feature on home page'),
            Field::toggle('is_published', 'Published')->default(true),
            Field::number('sort', 'Sort order')->default(0),
            Field::text('seo_title', 'SEO title'),
            Field::textarea('seo_description', 'SEO description')->rules('max:500'),
        ];
    }

    protected function columns(): array
    {
        return [['cover_image', '', 'image'], ['title', 'Title'], ['client.name', 'Client'], ['is_case_study', 'Case study', 'bool'], ['is_featured', 'Featured', 'bool'], ['is_published', 'Published', 'bool']];
    }
}
