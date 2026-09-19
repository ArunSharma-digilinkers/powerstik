@props(['items'])
{{-- Inner-page breadcrumb on a dark hero: [label => url] pairs, the last one is the current page --}}
<nav aria-label="Breadcrumb" class="mb-10 font-mono text-[11.5px] tracking-[.12em] text-muted uppercase">
    <ol class="flex flex-wrap items-center gap-x-2.5">
        <li><a href="{{ url('/') }}" class="transition-colors hover:text-white">Home</a></li>
        @foreach ($items as $label => $href)
            <li aria-hidden="true">/</li>
            <li>
                @if ($loop->last)
                    <span aria-current="page" class="text-white">{{ $label }}</span>
                @else
                    <a href="{{ $href }}" class="transition-colors hover:text-white">{{ $label }}</a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
