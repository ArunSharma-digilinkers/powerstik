@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $dispatch = Site::setting('dispatch_days', '2–5 days');

    // Section numbers run in order over the sections actually shown, so a sector
    // with nothing written about it yet still numbers cleanly.
    $shown = array_keys(array_filter([
        'challenges' => filled($industry->challenges),
        'supply' => true,
        'compliance' => filled($industry->compliance_notes),
        'work' => $cases->isNotEmpty(),
        'others' => $others->isNotEmpty(),
    ]));
    $num = fn (string $section) => sprintf('%02d', array_search($section, $shown) + 1);

    $intro = $industry->excerpt ?: 'Labels, cartons and the corrugated around them — designed, printed and dispatched from one floor in Haryana.';

    // Carries the sector through to the RFQ, where the industry select reads it
    $quoteUrl = url(config('site.quote_url')).'?industry='.rawurlencode($industry->name);
@endphp

<x-layouts.site :title="$industry->seo_title ?: $industry->name.' packaging'"
                :description="$industry->seo_description ?: $industry->excerpt ?: 'Labels, cartons and corrugated packaging for '.strtolower($industry->name).', printed in-house in Haryana since '.Site::setting('since_year', '2002').'.'">
    {{-- 1. Hero. The sector photograph is a cutout and sits in the hero beside the
         copy; a sector without one keeps the headline and the intro side by side. --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="industry-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[100px]">
            <x-site.breadcrumb :items="['Industries' => route('industries.index'), $industry->name => route('industries.show', $industry)]" />
            <div @class([
                'grid gap-9',
                'lg:min-h-[420px] lg:grid-cols-2 lg:items-center lg:gap-16' => $industry->hero_image,
                'lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20' => ! $industry->hero_image,
            ])>
                <div>
                    @if ($industry->note)
                        <p class="chip mb-[26px]">{{ $industry->note }}</p>
                    @endif
                    <h1 id="industry-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em] text-balance">
                        {{ $industry->name }} packaging.
                    </h1>
                    @if ($industry->hero_image)
                        <p class="mt-7 max-w-[46ch] text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">{{ $intro }}</p>
                    @endif
                </div>
                @if ($industry->hero_image)
                    <img src="{{ ImageStore::url($industry->hero_image) }}" srcset="{{ ImageStore::srcset($industry->hero_image) }}"
                         sizes="(min-width: 1024px) 604px, 100vw" alt="{{ $industry->name }} packaging printed by Powerstik"
                         class="w-full object-contain lg:max-h-[420px]">
                @else
                    <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">{{ $intro }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- 2. The constraint. The note is the one fact we hold for every sector. --}}
    @if ($industry->note)
        <section class="border-b border-rule bg-paper-mid" aria-labelledby="constraint-title">
            <div class="site-container flex flex-col gap-6 py-12 sm:flex-row sm:items-center sm:justify-between sm:gap-12 lg:py-[68px]">
                <div>
                    <p class="label-mark">The constraint</p>
                    <h2 id="constraint-title" class="mt-4 text-[clamp(1.75rem,1.2rem+1.8vw,2.75rem)] leading-[1.05] font-extrabold tracking-[-.035em]">{{ $industry->note }}</h2>
                </div>
                <p class="max-w-[42ch] text-[16px] leading-[1.55] text-body sm:text-right">
                    That is what we design the stock, the ink and the finish around before anyone opens the artwork.
                </p>
            </div>
        </section>
    @endif

    {{-- 3. Sector challenges (editor copy; hidden until written) --}}
    @if (filled($industry->challenges))
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="challenges-title">
            <x-site.section-header :num="$num('challenges')" kicker="What makes it hard" title="The failure we design against." id="challenges-title" />
            <div class="prose-site max-w-[68ch] pt-10">
                {!! $industry->challenges !!}
            </div>
        </section>
    @endif

    {{-- 4. What we supply --}}
    <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="supply-title">
        <x-site.section-header :num="$num('supply')" kicker="What we supply" title="Everything that goes round it." id="supply-title">
            Artwork, plates, print and box never change hands between vendors, so the colour on the label and the colour on the carton are the same colour.
        </x-site.section-header>

        @if (filled($industry->what_we_supply))
            <div class="prose-site max-w-[68ch] pt-10">
                {!! $industry->what_we_supply !!}
            </div>
        @endif

        <ul class="mt-10 grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($supplies as $supply)
                <li class="flex items-center gap-4 border-r border-b border-rule px-6 py-6 text-[17px] font-semibold tracking-[-.01em]">
                    <span class="h-[3px] w-[18px] shrink-0 bg-brand-500" aria-hidden="true"></span>
                    {{ $supply }}
                </li>
            @endforeach
        </ul>

        <dl class="mt-10 grid gap-px bg-rule-mid sm:grid-cols-3">
            @foreach ([
                ['Sheet or roll', 'Cut and counted, or wound to your applicator'],
                ['Offset · digital · flexo', 'All three in-house, so the run picks the press'],
                [$dispatch, 'Dispatch after your approval'],
            ] as [$term, $caption])
                <div class="flex flex-col-reverse bg-paper px-5 py-6">
                    <dt class="mt-1.5 font-mono text-[10.5px] leading-snug text-mono">{{ $caption }}</dt>
                    <dd class="text-[19px] font-extrabold tracking-[-.03em] sm:text-[21px]">{{ $term }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- 5. Compliance notes (editor copy; hidden until written) --}}
    @if (filled($industry->compliance_notes))
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="compliance-title">
            <x-site.section-header :num="$num('compliance')" kicker="Compliance" title="What the rulebook says." id="compliance-title" />
            <div class="prose-site mt-10 max-w-[68ch] border-l-[3px] border-brand-500 pl-7">
                {!! $industry->compliance_notes !!}
            </div>
        </section>
    @endif

    {{-- 6. Work in this sector --}}
    @if ($cases->isNotEmpty())
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="work-title">
            <x-site.section-header :num="$num('work')" kicker="In this sector" title="What it looked like." id="work-title">
                Problem, what we did, what changed.
            </x-site.section-header>
            <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($cases as $case)
                    <li class="flex flex-col border-r border-b border-rule px-7 py-9 lg:px-8 lg:py-10">
                        @if ($case->card_tag)
                            <p class="label-mark">{{ $case->card_tag }}</p>
                        @endif
                        <h3 class="mt-4 text-[22px] leading-[1.15] font-bold tracking-[-.025em]">{{ $case->title }}</h3>
                        @if ($case->summary)
                            <p class="mt-3.5 text-[15.5px] leading-[1.55] text-body">{{ $case->summary }}</p>
                        @endif
                        @if ($case->result_headline)
                            <p class="mt-auto pt-7 text-[17px] font-bold tracking-[-.02em]">
                                <span class="box-decoration-clone bg-brand-500">{{ $case->result_headline }}</span>
                            </p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 7. The other sectors --}}
    @if ($others->isNotEmpty())
        <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="others-title">
            <x-site.section-header :num="$num('others')" kicker="Other sectors" title="Different rule, same floor." id="others-title" />
            <ul class="flex flex-wrap gap-2.5 pt-10">
                @foreach ($others as $other)
                    <li>
                        <a href="{{ route('industries.show', $other) }}"
                           class="flex items-center gap-3 border border-rule-mid px-5 py-3 text-[16px] font-semibold transition-colors hover:border-ink hover:bg-brand-500">
                            {{ $other->name }}
                            @if ($other->note)
                                <span class="font-mono text-[10.5px] tracking-[.08em] text-mono uppercase">{{ $other->note }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <x-site.cta-band :body="'Send the size, the quantity and what it has to survive. A coordinator replies within one working day.'">
        <x-slot:title>Printing for<br> {{ strtolower($industry->name) }}?</x-slot:title>
        <x-site.cta-link :href="$quoteUrl" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
