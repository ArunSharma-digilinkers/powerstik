<label class="mt-6 flex items-center gap-3 text-sm font-medium text-zinc-700">
    <input type="hidden" name="{{ $field->name }}" value="0">
    <input type="checkbox" name="{{ $field->name }}" value="1" @checked(old($field->name, $value))
           class="size-4 rounded border-zinc-300 text-ink focus:ring-ink">
    {{ $field->label }}
</label>
@include('admin.resource.fields._meta')
