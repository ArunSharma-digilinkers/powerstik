@php
    use App\Models\Machine;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $labelsPerDay = Site::setting('labels_per_day', '5,00,000');
    $people = preg_replace('/\D/', '', Site::setting('people', '200+'));

    $strip = [
        ['4 disciplines', 'Design, labels, cartons, corrugated'],
        ['Offset · digital · flexo', 'All three in-house'],
        [$labelsPerDay, 'Labels a day off the presses'],
        ['To 9-ply', 'Corrugation capability'],
    ];

    // Counts per press category, so the teaser reflects the admin rather than a guess
    $byCategory = $machines->groupBy('category');
@endphp

<x-layouts.site title="Capabilities"
                description="Design, label printing, corrugated packaging and all three print technologies on one floor in Haryana. Artwork, plates, print and box never change hands between vendors.">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="capabilities-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Capabilities' => route('capabilities.index')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">Four disciplines, one roof</p>
                    <h1 id="capabilities-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Nothing leaves<br> the building.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Artwork, plates, print and box never change hands between vendors. One coordinator, one floor, ~{{ $people }} people — which is why a brief on Monday can ship as a pallet on Friday.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. Fact strip --}}
    <section class="border-t border-rule-dark bg-ink text-white" aria-label="Capabilities at a glance">
        <div class="site-container">
            <dl class="grid grid-cols-2 gap-px bg-rule-dark lg:grid-cols-4">
                @foreach ($strip as [$term, $caption])
                    <div class="flex flex-col-reverse bg-ink px-4 py-8 sm:px-6 lg:pt-10 lg:pb-[42px]">
                        <dt class="mt-2.5 font-mono text-[11px] leading-snug tracking-[.1em] text-muted uppercase sm:text-[11.5px]">{{ $caption }}</dt>
                        <dd class="text-[clamp(1.5rem,1.1rem+1.1vw,2.125rem)] leading-[1.05] font-extrabold tracking-[-.035em]">{{ $term }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 3. The four disciplines --}}
    <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="disciplines-title">
        <x-site.section-header num="01" kicker="What we do" title="Four disciplines." id="disciplines-title">
            They are sold as four things and delivered as one. A label brief that turns into a carton brief does not become a second purchase order.
        </x-site.section-header>

        <ul class="grid border-t border-l border-rule sm:grid-cols-2">
            @foreach ($disciplines as $slug => $discipline)
                <li>
                    <a href="{{ route('capabilities.show', $slug) }}" class="group flex h-full flex-col border-r border-b border-rule px-7 py-9 transition-colors hover:bg-paper-mid lg:px-9 lg:py-11">
                        <p class="label-mark">{{ $discipline['kicker'] }}</p>
                        <h3 class="mt-5 text-[27px] leading-[1.08] font-extrabold tracking-[-.03em]">
                            <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $discipline['nav'] }}</span>
                        </h3>
                        <p class="mt-4 max-w-[46ch] text-[16px] leading-[1.55] text-body">{{ $discipline['intro'] }}</p>
                        <span class="mt-auto pt-8 font-mono text-[11.5px] tracking-[.1em] uppercase">
                            {{ count($discipline['deliverables']) }} things included <span aria-hidden="true">→</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- 4. Machine park teaser --}}
    @if ($machines->isNotEmpty())
        <section class="mt-20 border-y border-rule bg-paper-mid lg:mt-[108px]" aria-labelledby="machines-title">
            <div class="site-container grid items-center gap-12 py-20 lg:grid-cols-[.9fr_1.1fr] lg:gap-[72px] lg:py-24">
                <div>
                    <p class="kicker mb-[18px]">02 / Machine park</p>
                    <h2 id="machines-title" class="text-[clamp(2rem,1.2rem+2.6vw,3.25rem)] leading-[1.02] font-extrabold tracking-[-.035em] text-balance">
                        We bought the machines so you wouldn’t have to chase them.
                    </h2>
                    <p class="mt-6 text-[17px] leading-[1.55] text-body">
                        {{ $machines->count() }} {{ \Illuminate\Support\Str::plural('press', $machines->count()) }} and lines on one floor, across {{ $byCategory->count() }} {{ \Illuminate\Support\Str::plural('process', $byCategory->count()) }}. No job is subcontracted out of sight.
                    </p>
                    <a href="{{ route('capabilities.machines') }}" class="link-underline mt-[30px] text-[15.5px]">See the spec of each one</a>
                </div>
                <dl class="grid grid-cols-2 gap-px bg-rule-mid">
                    @foreach (Machine::CATEGORIES as $key => $label)
                        @continue ($byCategory->get($key)?->isEmpty() ?? true)
                        <div class="flex flex-col-reverse bg-paper px-5 py-7">
                            <dt class="mt-1.5 font-mono text-[10.5px] leading-snug text-mono">{{ $byCategory[$key]->pluck('name')->join(' · ') }}</dt>
                            <dd class="text-[21px] font-extrabold tracking-[-.03em]">{{ $label }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>
    @endif

    {{-- 5. Where the work goes --}}
    @if ($industries->isNotEmpty())
        <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="sectors-title">
            <x-site.section-header num="03" kicker="Where it goes" title="Same floor, different rulebook." id="sectors-title">
                The press does not change between sectors. The stock, the ink and the thing it has to survive do.
            </x-site.section-header>
            <ul class="flex flex-wrap gap-2.5 pt-10">
                @foreach ($industries as $industry)
                    <li>
                        <a href="{{ route('industries.show', $industry) }}"
                           class="flex items-center gap-3 border border-rule-mid px-5 py-3 text-[16px] font-semibold transition-colors hover:border-ink hover:bg-brand-500">
                            {{ $industry->name }}
                            @if ($industry->note)
                                <span class="font-mono text-[10.5px] tracking-[.08em] text-mono uppercase">{{ $industry->note }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <x-site.cta-band body="Send the size, the quantity and what it has to survive. A coordinator replies with a price, a paper and a dispatch date within one working day.">
        <x-slot:title>One brief,<br> one supplier.</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
