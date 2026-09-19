<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class FaqController extends ResourceController
{
    protected string $model = Faq::class;

    protected string $slug = 'faqs';

    protected string $singular = 'FAQ';

    protected string $plural = 'FAQs';

    protected array $search = ['question', 'answer'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('question', 'Question')->required()->full(),
            Field::richtext('answer', 'Answer')->required(),
            Field::text('category', 'Group')->help('e.g. Orders, Artwork, Payment'),
            Field::number('sort', 'Sort order')->default(0),
            Field::toggle('is_published', 'Published')->default(true),
        ];
    }

    protected function columns(): array
    {
        return [['question', 'Question'], ['category', 'Group'], ['is_published', 'Published', 'bool']];
    }
}
