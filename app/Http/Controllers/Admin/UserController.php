<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class UserController extends ResourceController
{
    protected string $model = User::class;

    protected string $slug = 'users';

    protected string $singular = 'Staff account';

    protected string $plural = 'Staff accounts';

    protected array $search = ['name', 'email'];

    protected function query(): Builder
    {
        return User::query()->orderBy('name');
    }

    protected function fields(Model $record): array
    {
        return [
            Field::text('name', 'Name')->required(),
            Field::email('email', 'Email')->required()->rules(Rule::unique('users', 'email')->ignore($record->getKey())),
            Field::select('role', 'Role', User::ROLES)->required()->default(User::ROLE_EDITOR)
                ->help('Admin: everything. Editor: site content. Sales: leads only.'),
            Field::password('password', 'Password')->rules($record->exists ? 'nullable' : 'required')
                ->help($record->exists ? 'Leave empty to keep the current password.' : 'At least 10 characters.'),
            Field::toggle('is_active', 'Active')->default(true),
        ];
    }

    protected function columns(): array
    {
        return [['name', 'Name'], ['email', 'Email'], ['role', 'Role'], ['is_active', 'Active', 'bool'], ['last_login_at', 'Last login', 'date']];
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_if((int) $id === auth()->id(), 422, 'You cannot delete your own account.');

        return parent::destroy($id);
    }
}
