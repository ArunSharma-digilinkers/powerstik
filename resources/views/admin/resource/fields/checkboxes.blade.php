@php
    $selected = old($field->name, $value instanceof \Illuminate\Support\Collection ? $value->modelKeys() : ($value ?? []));
    $selected = array_map('strval', (array) $selected);
@endphp
<fieldset>
    <legend class="adm-label">{{ $field->label }}</legend>
    <input type="hidden" name="{{ $field->name }}" value="">
    @if ($field->options)
        <div class="flex flex-wrap gap-x-5 gap-y-2">
            @foreach ($field->options as $id => $label)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="{{ $field->name }}[]" value="{{ $id }}" @checked(in_array((string) $id, $selected, true))
                           class="size-4 rounded border-zinc-300 text-ink focus:ring-ink">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    @else
        <p class="text-sm text-zinc-500">None created yet.</p>
    @endif
</fieldset>
@include('admin.resource.fields._meta')
