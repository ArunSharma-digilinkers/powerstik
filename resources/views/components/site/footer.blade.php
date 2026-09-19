@php
    use App\Support\Site;
    $address = Site::setting('address');
    $email = Site::setting('email');
    $phone = Site::setting('phone');
    $social = collect(['linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube'])
        ->map(fn ($label, $key) => [$label, Site::setting($key)])->filter(fn ($s) => $s[1]);
@endphp
<footer class="bg-ink text-[14.5px] text-muted">
    <div class="site-container-wide grid gap-12 pt-16 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr] lg:gap-14 lg:pt-[76px]">
        <div class="sm:col-span-2 lg:col-span-1">
            <img src="{{ asset('images/brand/logo-white.png') }}" alt="Powerstik — better ideas" width="500" height="127" loading="lazy" class="h-[34px] w-auto">
            <p class="mt-6 max-w-[34ch] leading-[1.6] text-faint">Powerstik is a brand of Design India. Labels, cartons and corrugated packaging, designed and printed in-house.</p>
            <address class="mt-5 leading-[1.6] not-italic text-mono">
                @if ($address)
                    {!! nl2br(e($address)) !!}
                @else
                    Haryana, India
                @endif
            </address>
            @if ($phone || $email)
                <p class="mt-4 flex flex-col gap-1">
                    @if ($phone)<a href="{{ Site::telUrl($phone) }}" data-track="call" class="text-faint hover:text-white">{{ $phone }}</a>@endif
                    @if ($email)<a href="mailto:{{ $email }}" class="text-faint hover:text-white">{{ $email }}</a>@endif
                </p>
            @endif
            @if ($social->isNotEmpty())
                <ul class="mt-5 flex flex-wrap gap-x-5 gap-y-2">
                    @foreach ($social as [$label, $href])
                        <li><a href="{{ $href }}" target="_blank" rel="noopener" class="mono text-faint hover:text-white">{{ $label }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>

        @foreach (config('site.footer') as $heading => $links)
            <nav aria-label="{{ $heading }}">
                <h2 class="mono mb-[18px] tracking-[.12em] text-white">{{ $heading }}</h2>
                <ul class="flex flex-col gap-[11px]">
                    @foreach ($links as [$label, $href])
                        <li><a href="{{ url($href) }}" class="text-faint transition-colors hover:text-white">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @endforeach
    </div>

    <div class="site-container-wide pt-14">
        <div class="mono flex flex-col gap-3 border-t border-[#262626] pt-6 pb-24 tracking-[.08em] text-mono sm:pb-9 lg:flex-row lg:justify-between">
            <p>© {{ date('Y') }} Design India · Powerstik®</p>
            <p>{{ Site::exportCountries()->pluck('name')->join(' · ') }}</p>
        </div>
    </div>
</footer>
