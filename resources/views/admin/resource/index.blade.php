@php use App\Support\ImageStore; @endphp
<x-admin.layouts.app :title="$plural">
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <form method="GET" class="flex-1">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search {{ strtolower($plural) }}…" class="adm-input max-w-sm">
        </form>
        <a href="{{ route("admin.$slug.create") }}" class="adm-btn">+ New {{ strtolower($singular) }}</a>
    </div>

    <div class="adm-card overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-50 text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase">
                <tr>
                    @foreach ($columns as [$key, $label])
                        <th class="px-4 py-3">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($records as $record)
                    @php $url = route("admin.$slug.edit", $record->getKey()); @endphp
                    <tr class="hover:bg-zinc-50">
                        @foreach ($columns as $i => $column)
                            @php [$key, , $format] = $column + [2 => 'text']; $value = data_get($record, $key); @endphp
                            <td class="px-4 py-2.5 {{ $i === 0 && $format !== 'image' ? 'font-medium' : '' }}">
                                @switch($format)
                                    @case('image')
                                        @if ($value)
                                            <img src="{{ ImageStore::url($value) }}" alt="" class="size-10 rounded object-contain bg-zinc-100" loading="lazy">
                                        @else
                                            <span class="block size-10 rounded bg-zinc-100"></span>
                                        @endif
                                        @break
                                    @case('bool')
                                        <span @class(['inline-block rounded-full px-2 py-0.5 text-xs font-medium', 'bg-green-100 text-green-800' => $value, 'bg-zinc-100 text-zinc-500' => ! $value])>{{ $value ? 'Yes' : 'No' }}</span>
                                        @break
                                    @case('date')
                                        {{ $value?->format('d M Y') ?? '—' }}
                                        @break
                                    @default
                                        <a href="{{ $url }}" class="hover:text-brand-800">{{ \Illuminate\Support\Str::limit((string) ($value ?? '—'), 70) }}</a>
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) }}" class="px-4 py-10 text-center text-zinc-500">Nothing here yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $records->links() }}</div>
</x-admin.layouts.app>
