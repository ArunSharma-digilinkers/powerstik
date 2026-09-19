<?php

namespace App\Http\Controllers\Admin;

use App\Models\TeamMember;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class TeamMemberController extends ResourceController
{
    protected string $model = TeamMember::class;

    protected string $slug = 'team';

    protected string $singular = 'Team member';

    protected string $plural = 'Team';

    protected array $search = ['name', 'role'];

    protected function fields(Model $record): array
    {
        return [
            Field::text('name', 'Name')->required(),
            Field::text('role', 'Role / title'),
            Field::select('department', 'Department', TeamMember::DEPARTMENTS),
            Field::richtext('bio', 'Bio'),
            Field::text('quote', 'Quote')->help('Leadership cards only. Their own words, without quotation marks.'),
            Field::image('photo', 'Photo', 'team'),
            Field::toggle('is_leadership', 'Show on Leadership page'),
            Field::toggle('is_published', 'Published')->default(true),
            Field::number('sort', 'Sort order')->default(0),
        ];
    }

    protected function columns(): array
    {
        return [['photo', '', 'image'], ['name', 'Name'], ['role', 'Role'], ['is_leadership', 'Leadership', 'bool'], ['is_published', 'Published', 'bool']];
    }
}
