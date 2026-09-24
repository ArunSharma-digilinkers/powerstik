@include('admin.resource.fields._label')
@if ($value)
    <p class="mb-2 text-sm">
        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public_uploads')->url($value) }}" target="_blank" class="text-ink underline hover:no-underline">{{ basename($value) }}</a>
        <label class="ml-3 inline-flex items-center gap-1 text-zinc-600"><input type="checkbox" name="{{ $field->name }}_remove" value="1" class="rounded border-zinc-300"> Remove</label>
    </p>
@endif
<input id="f-{{ $field->name }}" type="file" name="{{ $field->name }}"
       class="block text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
@include('admin.resource.fields._meta')
