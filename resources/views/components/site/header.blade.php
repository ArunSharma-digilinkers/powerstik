@php
    use App\Support\Site;
    $phone = Site::setting('phone');
    $current = '/'.ltrim(request()->path(), '/');
@endphp
{{-- CMYK press colour bar: C, M, Y, K then brand red at 6× --}}
<div class="flex h-1" aria-hidden="true">
    <span class="flex-1 bg-cmyk-c"></span>
    <span class="flex-1 bg-cmyk-m"></span>
    <span class="flex-1 bg-cmyk-y"></span>
    <span class="flex-1 bg-cmyk-k"></span>
    <span class="flex-[6] bg-brand-500"></span>
</div>

{{-- Utility bar --}}
<div class="bg-ink font-mono text-[11.5px] tracking-[.06em] text-faint uppercase">
    <div class="site-container-wide flex h-[38px] items-center justify-between gap-6">
        <p class="truncate">A brand of Design India<span class="hidden md:inline"> · Branding / Design / Print / Packaging</span></p>
        <div class="flex shrink-0 items-center gap-[26px]">
            @foreach (config('site.utility') as [$label, $href])
                <a href="{{ url($href) }}" class="hidden transition-colors hover:text-white lg:inline">{{ $label }}</a>
            @endforeach
            @if ($phone)
                <a href="{{ Site::telUrl($phone) }}" data-track="call" class="text-white">{{ $phone }}</a>
            @endif
        </div>
    </div>
</div>

{{-- Header: sticky, translucent paper --}}
<header x-data="{ open: false }" @keydown.escape.window="open = false"
        class="sticky top-0 z-40 border-b border-rule bg-paper/94 backdrop-blur-[8px]">
    <div class="site-container-wide flex h-[72px] items-center justify-between gap-10 min-[1100px]:h-[84px]">
        <a href="{{ url('/') }}" class="shrink-0" aria-label="Powerstik home">
            <img src="{{ asset('images/brand/logo.png') }}" alt="Powerstik — better ideas" width="500" height="127" class="h-8 w-auto min-[1100px]:h-10">
        </a>

        <nav aria-label="Main" class="hidden min-[1100px]:block">
            <ul class="flex items-center gap-[30px] text-[14.5px] font-semibold tracking-[-.01em]">
                @foreach (config('site.nav') as $item)
                    <li>
                        <a href="{{ url($item[1]) }}" @if (str_starts_with($current, $item[1])) aria-current="page" @endif
                           class="flex items-center gap-2 transition-colors hover:text-brand-500 aria-[current=page]:text-brand-500">
                            @if ($item['flagship'] ?? false)
                                <span class="size-1.5 rounded-full bg-brand-500" aria-hidden="true"></span>
                            @endif
                            {{ $item[0] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ url(config('site.quote_url')) }}" data-track="quote_header"
               class="btn btn-red px-4 py-3 text-[14px] tracking-[-.01em] hover:bg-ink sm:px-[22px] sm:py-[13px]">Get a quote</a>
            <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-nav"
                    class="-mr-2 grid size-11 place-items-center min-[1100px]:hidden">
                <span class="sr-only">Menu</span>
                <svg x-show="!open" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="open" x-cloak class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <nav id="mobile-nav" x-show="open" x-collapse x-cloak aria-label="Main" class="border-t border-rule bg-paper min-[1100px]:hidden">
        <ul class="site-container-wide py-3 text-lg font-semibold tracking-[-.01em]">
            @foreach (config('site.nav') as $item)
                <li class="border-b border-rule last:border-0">
                    <a href="{{ url($item[1]) }}" class="flex items-center gap-2 py-3.5 hover:text-brand-500">
                        @if ($item['flagship'] ?? false)
                            <span class="size-1.5 rounded-full bg-brand-500" aria-hidden="true"></span>
                        @endif
                        {{ $item[0] }}
                    </a>
                </li>
            @endforeach
            @foreach (config('site.utility') as [$label, $href])
                <li><a href="{{ url($href) }}" class="mono block py-3 text-mono hover:text-ink">{{ $label }}</a></li>
            @endforeach
        </ul>
    </nav>
</header>
