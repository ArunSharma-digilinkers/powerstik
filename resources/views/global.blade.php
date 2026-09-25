@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $since = (int) Site::setting('since_year', '2002');
    $dispatch = Site::setting('dispatch_days', '2–5 days');
    $email = Site::setting('email');

    // The embedded enquiry shares its fields with /international-enquiry
    $bagErrors = $errors->getBag('international');
    $prev = fn (string $key, $default = null) => $bagErrors->any() ? old($key, $default) : $default;

    $shown = array_keys(array_filter([
        'map' => $countries->isNotEmpty(),
        'why' => true,
        'clients' => $exportClients->isNotEmpty(),
        'enquiry' => true,
    ]));
    $num = fn (string $section) => sprintf('%02d', array_search($section, $shown) + 1);

    $reasons = [
        ['Documentation', 'Invoice, packing list, certificate of origin and whatever else your clearing agent needs — prepared here, not chased afterwards.'],
        ['Incoterms', 'Quote on EXW, FOB, CFR or CIF. If you are not sure which, say so and we will explain what each one costs you.'],
        ['Port-ready packing', 'Packed for a container and a long journey, not for a van across town. Told us the port, and we pack for that leg.'],
        ['Colour on reorders', 'Your retained sample stays on our bench. The reorder is matched to it, not to a fresh reading of the file.'],
        ['One coordinator', 'The same named person from the first enquiry to the shipping documents, in your time zone’s working day.'],
        ['No agent in between', 'You are buying from the plant that prints it. Nothing is brokered, and nothing is marked up twice.'],
    ];

    // What an export quote actually needs, in the order the form asks for it
    $needs = [
        ['Country', 'Where it is going'],
        ['Port', 'Or the nearest one you clear through'],
        ['Product', 'Labels, cartons, corrugated'],
        ['Volume', 'A year, or a first order'],
        ['Incoterm', 'Or we will suggest one'],
    ];
@endphp

<x-layouts.site title="Global"
                :description="'Powerstik ships labels, cartons and corrugated packaging to '.$countries->count().' markets from Haryana, India — with the export documentation, Incoterms and port-ready packing handled by the same coordinator.'">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="global-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Global' => route('global')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $countries->count() }} {{ \Illuminate\Support\Str::plural('market', $countries->count()) }}</p>
                    <h1 id="global-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Your port,<br> our paperwork.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Exporting print is mostly a documents problem. We have been doing it long enough that the documents are not the part you have to manage — you tell us the country and the port, and the rest is our job.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. Proof strip --}}
    <section class="border-t border-rule-dark bg-ink text-white" aria-label="Exports in numbers">
        <div class="site-container">
            <dl class="grid grid-cols-2 gap-px bg-rule-dark lg:grid-cols-4">
                @foreach ([
                    [$countries->count(), 'Markets shipped to'],
                    [$since, 'Printing since'],
                    [trim(preg_replace('/\s*days?$/i', '', $dispatch)).' days', 'Dispatch after approval'],
                    ['One', 'Coordinator, start to documents'],
                ] as [$term, $caption])
                    <div class="flex flex-col-reverse bg-ink px-4 py-8 sm:px-6 lg:pt-10 lg:pb-[42px]">
                        <dt class="mt-2.5 font-mono text-[11px] leading-snug tracking-[.1em] text-muted uppercase sm:text-[11.5px]">{{ $caption }}</dt>
                        <dd class="text-[clamp(1.75rem,1.3rem+1.2vw,2.5rem)] leading-none font-extrabold tracking-[-.04em] tabular-nums">{{ $term }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 3. Where we ship --}}
    @if ($countries->isNotEmpty())
        <section id="map" class="on-dark bg-ink-deep text-white" aria-labelledby="markets-title">
            <div class="site-container py-20 lg:py-[100px]">
                <x-site.section-header :num="$num('map')" kicker="Where we ship" title="One standard, wherever it lands." :dark="true" id="markets-title">
                    The same press, the same retained sample and the same coordinator, whether the pallet goes to Sonipat or to Lagos.
                </x-site.section-header>

                <div class="mt-14 grid items-center gap-16 lg:grid-cols-[1.15fr_.85fr]">
                    <div>
                        @if ($media['export_map'])
                            <img src="{{ $media['export_map'] }}" width="1400" height="787" loading="lazy"
                                 class="hidden h-auto w-full min-[900px]:block"
                                 alt="Powerstik exports from Haryana, India to {{ $countries->pluck('name')->join(', ', ' and ') }}">
                            {{-- The artwork is the brochure's map and carries its nine pins. Two markets
                                 were confirmed after it was drawn, so the list beside it is the current one.
                                 See docs/design/brand.md — a redrawn map has been asked for. --}}
                            <p class="mt-6 hidden font-mono text-[11px] leading-[1.6] tracking-[.08em] text-muted uppercase min-[900px]:block">
                                Map artwork shows the nine markets on our company brochure. Bangladesh and the USA were added after it was drawn.
                            </p>
                        @else
                            <svg viewBox="0 0 620 420" class="hidden h-auto w-full overflow-visible min-[900px]:block" data-inview role="img"
                                 aria-label="Export routes from Haryana, India to {{ $countries->pluck('name')->join(', ', ' and ') }}">
                                @foreach ($arcs as $i => $arc)
                                    <path class="ps-arc" d="{{ $arc['d'] }}" fill="none" stroke="{{ $i % 2 ? '#4A4A4A' : '#C7FF00' }}" stroke-width="1.1"
                                          style="animation-delay: {{ 0.25 + $i * 0.14 }}s"/>
                                @endforeach
                                @foreach ($arcs as $arc)
                                    <circle cx="{{ $arc['x'] }}" cy="{{ $arc['y'] }}" r="3.6" fill="#C7FF00"/>
                                    <text x="{{ $arc['lx'] }}" y="{{ $arc['ly'] }}" text-anchor="{{ $arc['anchor'] }}" fill="#C6C4C5" class="font-mono text-[11.5px] tracking-[.06em]">{{ $arc['name'] }}</text>
                                @endforeach
                                <circle cx="96" cy="300" r="7" fill="#FFFFFF"/>
                                <circle cx="96" cy="300" r="16" fill="none" stroke="#4A4A4A"/>
                                <text x="92" y="340" fill="#FFFFFF" class="text-[17px] font-bold tracking-[-.02em]">Haryana, India</text>
                            </svg>
                        @endif
                    </div>

                    <div>
                        {{-- Borders per cell, not a gap-px background: an odd number of
                             markets would otherwise leave a filler block in the last row. --}}
                        <ul class="grid grid-cols-2 border-t border-l border-rule-dark">
                            @foreach ($countries as $country)
                                <li class="border-r border-b border-rule-dark px-[18px] py-5">
                                    <p class="text-[19px] font-bold tracking-[-.02em]">{{ $country->name }}</p>
                                    @if ($country->note)
                                        <p class="mt-1.5 font-mono text-[10.5px] tracking-[.08em] text-muted uppercase">{{ $country->note }}</p>
                                    @endif
                                    @if ($country->blurb)
                                        <p class="mt-2 text-[14px] leading-[1.5] text-on-dark">{{ $country->blurb }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-7 text-[15.5px] leading-[1.55] text-on-dark">
                            Not on the list? It usually only means nobody has asked yet. <a href="#enquiry" class="border-b-2 border-brand-500 font-semibold text-white hover:border-white">Tell us the market</a>.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 4. Why partner with us --}}
    <section id="why" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="why-title">
        <x-site.section-header :num="$num('why')" kicker="Why partner with us" title="The part nobody quotes for." id="why-title">
            The print is the easy half. What makes an export order go wrong is almost always a document, a packing decision or a colour that drifted between orders.
        </x-site.section-header>

        <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($reasons as $i => [$title, $body])
                <li class="flex flex-col border-r border-b border-rule px-7 py-9 lg:px-8 lg:py-10">
                    <span class="font-mono text-[11.5px] text-faint">{{ sprintf('%02d', $i + 1) }}</span>
                    <h3 class="mt-5 text-[22px] leading-[1.1] font-bold tracking-[-.025em]">{{ $title }}</h3>
                    <p class="mt-3.5 text-[15.5px] leading-[1.55] text-body">{{ $body }}</p>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- 5. Named export clients (hidden until a client gives permission) --}}
    @if ($exportClients->isNotEmpty())
        <section id="clients" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="clients-title">
            <x-site.section-header :num="$num('clients')" kicker="Already shipping" title="Who we ship to." id="clients-title">
                Named with their permission. Plenty more are not, which is the normal state of a supply agreement.
            </x-site.section-header>
            <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($exportClients as $client)
                    <li class="flex h-[128px] flex-col items-center justify-center gap-3 border-r border-b border-rule px-5">
                        @if ($client->logo)
                            <img src="{{ ImageStore::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy" class="max-h-10 w-auto logo-mark">
                        @else
                            <span class="text-center text-[22px] font-bold tracking-[-.035em] text-logo">{{ $client->name }}</span>
                        @endif
                        @if ($client->exportCountry)
                            <span class="font-mono text-[10.5px] tracking-[.08em] text-mono uppercase">{{ $client->exportCountry->name }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 6. The enquiry. Same fields and same intake as /international-enquiry. --}}
    <section id="enquiry" class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="enquiry-title">
        <x-site.section-header :num="$num('enquiry')" kicker="International enquiry" title="Tell us the market." id="enquiry-title">
            Country and port are the two that change the answer. The rest we can work out with you.
        </x-site.section-header>

        <div class="grid gap-12 pt-12 lg:grid-cols-[1fr_340px] lg:gap-20">
            <form method="POST" action="{{ route('enquiry.store', 'international') }}" id="form" class="scroll-mt-28">
                @csrf
                <x-site.honeypot bag="international" />
                <x-site.form-errors bag="international" />

                @include('enquiry.steps.market', ['intent' => 'international', 'countries' => $countries->pluck('name')])

                <div class="mt-9 border-t border-rule pt-8">
                    @include('enquiry.steps.contact', ['intent' => 'international'])
                </div>

                <div class="mt-9 border-t border-rule pt-7">
                    <button type="submit" data-track="international_submit" class="btn btn-brand w-full px-8 py-[18px] text-[16px] sm:w-auto">Send the enquiry</button>
                    <p class="mt-4 text-[13.5px] leading-[1.5] text-mono">We use this to answer your enquiry and nothing else. No list, no reselling.</p>
                </div>
            </form>

            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="bg-paper-mid p-7">
                    <h2 class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">What we need to quote</h2>
                    <dl class="mt-5 border-t border-rule-mid">
                        @foreach ($needs as [$term, $caption])
                            <div class="border-b border-rule-mid py-3 last:border-0">
                                <dt class="text-[15.5px] font-bold tracking-[-.01em]">{{ $term }}</dt>
                                <dd class="mt-0.5 text-[14px] leading-[1.45] text-body">{{ $caption }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                @if ($email)
                    <p class="mt-8"><a href="mailto:{{ $email }}" class="link-underline break-all">{{ $email }}</a></p>
                @endif
                @if ($whatsapp)
                    <p class="mt-3"><a href="{{ $whatsapp }}" target="_blank" rel="noopener" data-track="whatsapp" class="link-underline">Or ask on WhatsApp</a></p>
                @endif
            </aside>
        </div>
    </section>

    <x-site.cta-band body="Domestic job, or not sure yet? The standard quote form takes the same spec.">
        <x-slot:title>Shipping<br> somewhere else?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
