<label class="mt-6 flex items-center gap-3 text-sm font-medium text-zinc-700">
    <input type="hidden" name="{{ $field->name }}" value="0">
    <input type="checkbox" name="{{ $field->name }}" value="1" @checked(old($field->name, $value))
           class="size-4 rounded border-zinc-300 text-brand-500 focus:ring-brand-500">
    {{ $field->label }}
</label>
@include('admin.resource.fields._meta')
