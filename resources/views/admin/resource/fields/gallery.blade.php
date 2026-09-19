@include('admin.resource.fields._label')
@if ($value)
    <div class="mb-3 flex flex-wrap gap-3">
        @foreach ($value as $path)
            <label class="group relative block size-24 overflow-hidden rounded-md border border-zinc-200 bg-zinc-50">
                <img src="{{ \App\Support\ImageStore::url($path) }}" alt="" class="size-full object-cover">
                <span class="absolute inset-x-0 bottom-0 flex items-center gap-1 bg-white/90 px-1.5 py-0.5 text-xs">
                    <input type="checkbox" name="{{ $field->name }}_remove[]" value="{{ $path }}" class="rounded border-zinc-300"> Remove
                </span>
            </label>
        @endforeach
    </div>
@endif
<input id="f-{{ $field->name }}" type="file" name="{{ $field->name }}_new[]" multiple accept="image/jpeg,image/png,image/webp"
       class="block text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
<p class="mt-1 text-xs text-zinc-500">Select several images to add. Up to 20 per save.</p>
@include('admin.resource.fields._meta')
@error($field->name.'_new.*')<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
