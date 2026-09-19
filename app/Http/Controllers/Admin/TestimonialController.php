<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\Testimonial;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class TestimonialController extends ResourceController
{
    protected string $model = Testimonial::class;

    protected string $slug = 'testimonials';

    protected string $singular = 'Testimonial';

    protected string $plural = 'Testimonials';

    protected array $search = ['person', 'company', 'quote'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('person', 'Person')->required(),
            Field::text('role', 'Role'),
            Field::text('company', 'Company'),
            Field::select('client_id', 'Linked client', Client::ordered()->pluck('name', 'id')->all()),
            Field::textarea('quote', 'Quote')->required(),
            Field::image('photo', 'Photo', 'testimonials'),
            Field::url('video_url', 'Video URL')->help('YouTube link for a video testimonial.'),
            Field::toggle('is_published', 'Published')->default(true),
            Field::number('sort', 'Sort order')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['person', 'Person'], ['company', 'Company'], ['is_published', 'Published', 'bool'], ['sort', 'Sort']];
    }
}
