<?php

namespace App\Http\Controllers\Admin;

use App\Models\GlossaryTerm;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class GlossaryTermController extends ResourceController
{
    protected string $model = GlossaryTerm::class;

    protected string $slug = 'glossary';

    protected string $singular = 'Glossary term';

    protected string $plural = 'Glossary';

    protected array $search = ['term', 'definition'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('term', 'Term')->required(),
            Field::slug()->help('Leave empty to generate from the term.'),
            Field::richtext('definition', 'Definition')->required(),
        ];
    }

    protected function columns(): array
    {
        return [['term', 'Term'], ['slug', 'Slug']];
    }
}
