@include('admin.resource.fields._label')
<div class="flex items-start gap-4" x-data="{ preview: null }">
    <div class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-md border border-zinc-200 bg-zinc-50">
        <template x-if="preview"><img :src="preview" alt="" class="size-full object-contain"></template>
        @if ($value)
            <img x-show="!preview" src="{{ \App\Support\ImageStore::url($value) }}" alt="" class="size-full object-contain">
        @else
            <span x-show="!preview" class="text-xs text-zinc-400">No image</span>
        @endif
    </div>
    <div class="space-y-2 text-sm">
        <input id="f-{{ $field->name }}" type="file" name="{{ $field->name }}"
               accept="image/jpeg,image/png,image/webp{{ $field->allowSvg ? ',image/svg+xml' : '' }}"
               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
               class="block text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
        @if ($value)
            <label class="flex items-center gap-2 text-zinc-600">
                <input type="checkbox" name="{{ $field->name }}_remove" value="1" class="rounded border-zinc-300"> Remove image
            </label>
        @endif
    </div>
</div>
@include('admin.resource.fields._meta')
