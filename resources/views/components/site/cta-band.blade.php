@props(['id' => 'cta-title', 'body' => null])
{{-- Final CTA band: the one place the brand lime runs as a full field, as it does in the brochure.
     Type on it is always ink. Heading in `title`, action links (x-site.cta-link) in the slot. --}}
<section {{ $attributes->class(['bg-brand-500 text-ink']) }} aria-labelledby="{{ $id }}">
    <div class="site-container grid items-center gap-12 py-20 lg:grid-cols-[1.1fr_.9fr] lg:gap-[72px] lg:py-[104px]">
        <div>
            <h2 id="{{ $id }}" class="text-[clamp(2.25rem,1rem+4vw,4.5rem)] leading-[.96] font-extrabold tracking-[-.04em] text-balance">{{ $title }}</h2>
            @if ($body)
                <p class="mt-7 max-w-[50ch] text-[17px] leading-[1.5] text-ink/80 sm:text-[19px]">{{ $body }}</p>
            @endif
        </div>
        <div class="flex flex-col gap-3">
            {{ $slot }}
        </div>
    </div>
</section>
