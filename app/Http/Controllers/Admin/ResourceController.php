<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin\Field;
use App\Support\ImageStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Stevebauman\Purify\Facades\Purify;

/**
 * Generic list / create / edit / delete screens for a content model.
 * Subclasses declare the model, its form fields and its list columns.
 */
abstract class ResourceController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    /** Route segment, e.g. "industries" → admin.industries.* */
    protected string $slug;

    protected string $singular;

    protected string $plural;

    /** Columns searched by the list's search box. */
    protected array $search = [];

    protected int $perPage = 25;

    /** @return list<Field> */
    abstract protected function fields(Model $record): array;

    /**
     * List columns as [attribute, label, format]. Attribute may use dot notation
     * for relations. Format: text (default), bool, image, date.
     *
     * @return list<array{0: string, 1: string, 2?: string}>
     */
    abstract protected function columns(): array;

    protected function query(): Builder
    {
        return ($this->model)::query()->ordered();
    }

    public function index(Request $request): View
    {
        $query = $this->query();

        if ($term = trim((string) $request->query('q'))) {
            $query->where(function (Builder $q) use ($term) {
                foreach ($this->search as $column) {
                    $q->orWhere($column, 'like', "%{$term}%");
                }
            });
        }

        return view('admin.resource.index', [
            'records' => $query->paginate($this->perPage)->withQueryString(),
            'columns' => $this->columns(),
        ] + $this->meta());
    }

    public function create(): View
    {
        return $this->form(new $this->model);
    }

    public function store(Request $request): RedirectResponse
    {
        $record = $this->save($request, new $this->model);

        return redirect()->route("admin.{$this->slug}.edit", $record->getKey())
            ->with('status', "{$this->singular} created.");
    }

    public function edit(string $id): View
    {
        return $this->form($this->find($id));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $this->save($request, $this->find($id));

        return back()->with('status', "{$this->singular} saved.");
    }

    public function destroy(string $id): RedirectResponse
    {
        $record = $this->find($id);

        foreach ($this->fields($record) as $field) {
            match ($field->type) {
                'image' => ImageStore::delete($record->{$field->name}),
                'gallery' => array_map(ImageStore::delete(...), $record->{$field->name} ?? []),
                'file' => $record->{$field->name} && Storage::disk('public_uploads')->delete($record->{$field->name}),
                default => null,
            };
        }

        $record->delete();

        return redirect()->route("admin.{$this->slug}.index")->with('status', "{$this->singular} deleted.");
    }

    protected function find(string $id): Model
    {
        return ($this->model)::query()->whereKey($id)->firstOrFail();
    }

    protected function form(Model $record): View
    {
        return view('admin.resource.form', [
            'record' => $record,
            'fields' => $this->fields($record),
        ] + $this->meta());
    }

    protected function meta(): array
    {
        return ['slug' => $this->slug, 'singular' => $this->singular, 'plural' => $this->plural];
    }

    protected function save(Request $request, Model $record): Model
    {
        $fields = $this->fields($record);
        $data = $request->validate($this->rules($fields, $record));
        $relations = [];

        foreach ($fields as $field) {
            $name = $field->name;

            switch ($field->type) {
                case 'toggle':
                    $record->{$name} = $request->boolean($name);
                    break;

                case 'checkboxes':
                    $relations[$name] = $data[$name] ?? [];
                    break;

                case 'password':
                    if (filled($data[$name] ?? null)) {
                        $record->{$name} = $data[$name];
                    }
                    break;

                case 'richtext':
                    $record->{$name} = filled($data[$name] ?? null) ? Purify::clean($data[$name]) : null;
                    break;

                case 'keyvalue':
                    $record->{$name} = collect($data[$name] ?? [])
                        ->filter(fn ($row) => filled($row['label'] ?? null))
                        ->map(fn ($row) => ['label' => $row['label'], 'value' => $row['value'] ?? ''])
                        ->values()->all();
                    break;

                case 'image':
                    if ($request->hasFile($name) || $request->boolean("{$name}_remove")) {
                        ImageStore::delete($record->{$name});
                        $record->{$name} = $request->hasFile($name) ? ImageStore::store($request->file($name), $field->dir) : null;
                    }
                    break;

                case 'gallery':
                    $keep = array_values(array_diff($record->{$name} ?? [], $request->input("{$name}_remove", [])));
                    array_map(ImageStore::delete(...), array_diff($record->{$name} ?? [], $keep));
                    foreach ($request->file("{$name}_new", []) as $upload) {
                        $keep[] = ImageStore::store($upload, $field->dir);
                    }
                    $record->{$name} = $keep;
                    break;

                case 'file':
                    if ($request->hasFile($name) || $request->boolean("{$name}_remove")) {
                        if ($record->{$name}) {
                            Storage::disk('public_uploads')->delete($record->{$name});
                        }
                        $record->{$name} = $request->hasFile($name) ? $this->storeFile($request->file($name), $field->dir) : null;
                    }
                    break;

                default:
                    // Blank inputs fall back to the field default (e.g. sort → 0 for NOT NULL columns).
                    $record->{$name} = $data[$name] ?? $field->default;
            }
        }

        $this->beforeSave($record, $request);
        $record->save();

        foreach ($relations as $relation => $ids) {
            $record->{$relation}()->sync($ids);
        }

        return $record;
    }

    /** Hook for subclasses to set derived attributes (e.g. author). */
    protected function beforeSave(Model $record, Request $request): void {}

    protected function rules(array $fields, Model $record): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $name = $field->name;
            $base = match ($field->type) {
                'slug' => ['nullable', 'alpha_dash', 'max:255', Rule::unique($record->getTable(), $name)->ignore($record->getKey())],
                'textarea' => ['nullable', 'string', 'max:10000'],
                'richtext' => ['nullable', 'string', 'max:200000'],
                'number' => ['nullable', 'integer'],
                'email' => ['nullable', 'email', 'max:255'],
                'password' => ['nullable', 'string', 'min:10', 'max:255'],
                'url' => ['nullable', 'url', 'max:1000'],
                'datetime' => ['nullable', 'date'],
                'toggle' => [],
                'select' => ['nullable', Rule::in(array_keys($field->options))],
                'checkboxes' => ['nullable', 'array'],
                'image' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp'.($field->allowSvg ? ',svg' : '')],
                'gallery' => [],
                'file' => ['nullable', 'file', 'max:30720', 'mimes:pdf,zip,doc,docx,xls,xlsx'],
                'keyvalue' => ['nullable', 'array'],
                default => ['nullable', 'string', 'max:255'],
            };

            // "required" on an upload means "required when there is no file yet".
            $extra = in_array($field->type, ['image', 'file'], true) && $record->{$name}
                ? array_values(array_filter($field->rules, fn ($r) => $r !== 'required'))
                : $field->rules;

            $required = in_array('required', $extra, true);
            $rules[$name] = array_merge($required ? array_values(array_filter($base, fn ($r) => $r !== 'nullable')) : $base, $extra);

            if ($field->type === 'checkboxes') {
                $rules["{$name}.*"] = ['integer', Rule::in(array_keys($field->options))];
            } elseif ($field->type === 'keyvalue') {
                $rules["{$name}.*.label"] = ['nullable', 'string', 'max:100'];
                $rules["{$name}.*.value"] = ['nullable', 'string', 'max:255'];
            } elseif ($field->type === 'gallery') {
                $rules["{$name}_new"] = ['nullable', 'array', 'max:20'];
                $rules["{$name}_new.*"] = ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp'];
                $rules["{$name}_remove"] = ['nullable', 'array'];
            }
        }

        return $rules;
    }

    private function storeFile($upload, string $dir): string
    {
        $name = Str::slug(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';

        return $upload->storeAs(trim($dir, '/'), $name.'-'.Str::lower(Str::random(6)).'.'.$upload->getClientOriginalExtension(), 'public_uploads');
    }
}
