@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
@endphp

<x-layouts.site title="Industries"
                description="Nine sectors, nine rulebooks. Acid and heat for battery, legibility for pharma, grease for QSR — we know the constraint before you name it.">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="industries-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Industries' => route('industries.index')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $industries->count() }} sectors</p>
                    <h1 id="industries-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Every sector<br> has a rulebook.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    A label is never just a label. It is the one thing on the product that has to survive whatever the product goes through — acid, heat, grease, a cold chain, a regulator. Pick your sector and we will tell you the constraint before you name it.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. The sectors --}}
    @if ($industries->isNotEmpty())
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="sectors-title">
            <x-site.section-header num="01" kicker="Where our work goes" title="Pick the one you are in." id="sectors-title">
                Same plant, same coordinator, same press. What changes is the stock, the ink and the thing it has to withstand.
            </x-site.section-header>

            <ul class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-9 lg:gap-y-12">
                @foreach ($industries as $industry)
                    <li>
                        <a href="{{ route('industries.show', $industry) }}" class="group block h-full">
                            <div class="aspect-[4/3] overflow-hidden bg-well">
                                @if ($industry->card_image)
                                    <img src="{{ ImageStore::url($industry->card_image) }}" srcset="{{ ImageStore::srcset($industry->card_image) }}"
                                         sizes="(min-width: 1024px) 420px, (min-width: 640px) 50vw, 100vw"
                                         alt="{{ $industry->name }} packaging printed by Powerstik"
                                         loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
                                @endif
                            </div>
                            <div class="mt-3.5 flex items-baseline justify-between gap-4 border-t border-ink px-0.5 pt-4">
                                <h2 class="-mx-1 px-1 text-[22px] font-bold tracking-[-.02em] transition-colors group-hover:bg-brand-500">{{ $industry->name }}</h2>
                                @if ($industry->note)
                                    <span class="shrink-0 text-right font-mono text-[11px] tracking-[.08em] text-mono uppercase">{{ $industry->note }}</span>
                                @endif
                            </div>
                            @if ($industry->excerpt)
                                <p class="mt-3 px-0.5 text-[15.5px] leading-[1.55] text-body">{{ $industry->excerpt }}</p>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 3. Why the constraint comes first --}}
    <section class="mt-20 lg:mt-[108px]" aria-labelledby="constraint-title">
        <div class="on-dark bg-ink text-white">
            <div class="site-container grid gap-12 py-20 lg:grid-cols-[1fr_1fr] lg:gap-20 lg:py-[100px]">
                <div>
                    <p class="chip mb-[26px]">02 / How we work</p>
                    <h2 id="constraint-title" class="text-[clamp(2rem,1.2rem+3vw,3.5rem)] leading-[1] font-extrabold tracking-[-.04em] text-balance">
                        The constraint<br> comes first.
                    </h2>
                    <p class="mt-6 max-w-[52ch] text-[17px] leading-[1.55] text-on-dark">
                        Most print briefs start with the artwork. Ours start with the failure — what the label has to survive, and what happens to you when it does not. Get that right and the artwork has somewhere safe to sit. Get it wrong and the prettiest label in the country peels off in a warehouse.
                    </p>
                    <p class="mt-8">
                        <a href="{{ url(config('site.quote_url')) }}" data-track="quote_industries" class="btn btn-brand px-7 py-4 text-[15.5px]">Tell us your constraint</a>
                    </p>
                </div>
                <dl class="grid grid-cols-2 gap-px self-start bg-[#2e2e2e]">
                    @foreach ([
                        ['Stock', 'Chosen for what it faces, not for what it costs'],
                        ['Ink & finish', 'Food-safe, child-safe, grease- and acid-stable'],
                        ['Proof', 'Approved before a plate is made'],
                        ['PDI', 'Matched to the retained sample before dispatch'],
                    ] as [$term, $caption])
                        <div class="flex flex-col-reverse bg-ink px-5 py-7">
                            <dt class="mt-2 font-mono text-[10.5px] leading-snug text-muted">{{ $caption }}</dt>
                            <dd class="text-[20px] font-extrabold tracking-[-.03em] sm:text-[23px]">{{ $term }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    <x-site.cta-band class="mt-0" body="Name the sector and the job. A coordinator replies with a price, a paper and a dispatch date within one working day.">
        <x-slot:title>Not sure which<br> rulebook applies?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
