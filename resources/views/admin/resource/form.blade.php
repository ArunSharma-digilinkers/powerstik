<x-admin.layouts.app :title="($record->exists ? 'Edit ' : 'New ').strtolower($singular)">
    <div class="mb-4 text-sm"><a href="{{ route("admin.$slug.index") }}" class="text-zinc-500 hover:text-ink">← {{ $plural }}</a></div>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $record->exists ? route("admin.$slug.update", $record->getKey()) : route("admin.$slug.store") }}"
          class="max-w-4xl">
        @csrf
        @if ($record->exists) @method('PUT') @endif

        <div class="adm-card grid gap-5 p-6 sm:grid-cols-2">
            @foreach ($fields as $field)
                <div @class(['sm:col-span-2' => $field->full])>
                    @include('admin.resource.fields.'.$field->type, [
                        'field' => $field,
                        'value' => $record->exists ? $record->{$field->name} : $field->default,
                    ])
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button class="adm-btn">{{ $record->exists ? 'Save changes' : 'Create '.strtolower($singular) }}</button>
            <a href="{{ route("admin.$slug.index") }}" class="adm-btn-secondary">Cancel</a>
        </div>
    </form>

    @if ($record->exists)
        <form method="POST" action="{{ route("admin.$slug.destroy", $record->getKey()) }}" class="mt-10 max-w-4xl border-t border-zinc-200 pt-6"
              x-data="{ sure: false }">
            @csrf @method('DELETE')
            <button type="button" x-show="!sure" @click="sure = true" class="text-sm font-medium text-brand-600 hover:text-brand-800">Delete this {{ strtolower($singular) }}…</button>
            <span x-show="sure" x-cloak class="flex items-center gap-3 text-sm">
                This cannot be undone.
                <button class="adm-btn">Yes, delete</button>
                <button type="button" @click="sure = false" class="adm-btn-secondary">Keep</button>
            </span>
        </form>
    @endif
</x-admin.layouts.app>
