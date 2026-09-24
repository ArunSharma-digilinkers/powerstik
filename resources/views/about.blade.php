@php
    use App\Models\JobOpening;
    use App\Support\ImageStore;
    use App\Support\Site;

    $since = (int) Site::setting('since_year', '2002');
    $people = preg_replace('/\D/', '', Site::setting('people', '200+'));
    $dispatch = Site::setting('dispatch_days', '2–5 days');
    $whatsapp = Site::whatsappUrl();
    $careersEmail = Site::setting('careers_email');
    $walkthrough = Site::setting('walkthrough_url');

    // Section numbers run in order over the sections that are actually shown
    $shown = array_keys(array_filter([
        'story' => $milestones->isNotEmpty(), 'leadership' => $leaders->isNotEmpty(), 'design-india' => true, 'people' => true,
        'quality' => true, 'infrastructure' => true, 'sustainability' => (bool) $sustainability, 'careers' => true,
    ]));
    $num = fn (string $section) => sprintf('%02d', array_search($section, $shown) + 1);

    $proof = [
        [$since, 'Founded as a design setup'],
        [Site::setting('people', '200+'), 'People across design, plant & QC'],
        [Site::setting('clients', '1,500+'), 'Domestic clients served'],
        [Site::setting('export_countries', '8'), 'Export countries'],
    ];

    // Fixed copy from the About design. Milestones, leaders, roles and the
    // certification / sustainability lists come from the database.
    $teams = [
        ['Design studio', 'Packaging and label design, dielines, 3D mockups and artwork prep. Where every job starts.', 'team_design', 'Designers at their screens in the Powerstik studio'],
        ['Sales & coordination', 'One named coordinator per client, from enquiry through dispatch and reorder.', 'team_sales', 'The coordination desk'],
        ['Production', 'Pressmen, flexo operators and the corrugation crew running three shifts.', 'team_production', 'Pressmen at the Heidelberg SM-74'],
        ['Quality & dispatch', 'Incoming inspection, in-process checks, PDI, packing and export documentation.', 'team_quality', 'The QC bench with retained samples'],
    ];

    $stages = [
        ['Incoming material', 'Board, paper and ink checked against spec on arrival — GSM, caliper, shade and adhesive batch recorded.'],
        ['In-process control', 'Colour read against the approved proof at set intervals; registration, die accuracy and lamination checked on the run.'],
        ['Pre-dispatch (PDI)', 'Sample pulled from the packed lot, matched to the retained sample, counted and signed before the truck loads.'],
    ];

    $tests = [
        ['GSM & caliper', 'Incoming'],
        ['Shade match to retained sample', 'Incoming + PDI'],
        ['Colour density / delta-E', 'In-process'],
        ['Registration & die accuracy', 'In-process'],
        ['Adhesion / peel', 'In-process'],
        ['Acid & heat resistance', 'Battery stock'],
        ['Bursting strength & ply bond', 'Corrugated'],
        ['Box compression', 'Corrugated'],
        ['Count & packing check', 'PDI'],
    ];

    $plantFacts = [
        ['3 shifts', 'Production running'],
        ['Offset · digital · flexo', 'Print processes in-house'],
        ['To 9-ply', 'Corrugation capability'],
        [$dispatch, 'Dispatch after approval'],
    ];

    $apply = fn (?string $subject = null) => $careersEmail
        ? 'mailto:'.$careersEmail.($subject ? '?subject='.rawurlencode($subject) : '')
        : url('/contact');
@endphp

<x-layouts.site title="About us" description="Powerstik is the print and packaging arm that grew out of a design studio. Since {{ $since }}: one plant in Haryana, ~{{ $people }} people and the same two founders.">
    {{-- 1. Page hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="about-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['About us' => url('/about')]" />
            <div class="grid gap-8 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="mb-[26px] flex items-center gap-3.5 font-mono text-[12px] tracking-[.14em] text-brand-500 uppercase">
                        <span class="h-0.5 w-[46px] shrink-0 bg-brand-500" aria-hidden="true"></span>
                        Since {{ $since }} · Haryana, India
                    </p>
                    <h1 id="about-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        It started at a<br> design table.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Powerstik is the print and packaging arm that grew out of a design studio — not a press that later hired designers. {{ now()->year - $since }} years, one plant, ~{{ $people }} people, and the same two brothers still signing off on jobs.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. Proof strip (static here: context, not a headline claim) --}}
    <section class="border-t border-rule-dark bg-ink text-white" aria-label="Powerstik in numbers">
        <div class="site-container">
            <dl class="grid grid-cols-2 gap-px bg-rule-dark lg:grid-cols-4">
                @foreach ($proof as [$value, $label])
                    <div class="flex flex-col-reverse bg-ink px-4 py-8 sm:px-6 lg:pt-10 lg:pb-[42px]">
                        <dt class="mt-2.5 font-mono text-[11px] tracking-[.1em] text-muted uppercase sm:text-[11.5px]">{{ $label }}</dt>
                        <dd class="text-[clamp(2rem,1.5rem+1.2vw,2.75rem)] leading-none font-extrabold tracking-[-.04em] tabular-nums">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 3. Our story: interactive timeline --}}
    @if ($milestones->isNotEmpty())
        <section id="story" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="story-title"
                 x-data="{
                     active: {{ $active }},
                     years: @js($milestones->pluck('year')),
                     select(i, focus = false) {
                         this.active = (i + this.years.length) % this.years.length;
                         const tab = this.$refs.tabs.children[this.active];
                         if (focus) tab.focus();
                         tab.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                         const url = new URL(location.href);
                         url.searchParams.set('year', this.years[this.active]);
                         history.replaceState(null, '', url);
                     },
                 }">
            <x-site.section-header :num="$num('story')" kicker="Our story" title="Every machine has a reason." id="story-title">
                We never bought equipment speculatively. Each addition answered a job we had already said yes to. Pick a year.
            </x-site.section-header>

            <div class="mt-11 overflow-x-auto border-b border-rule [scrollbar-width:none] snap-x">
                <div x-ref="tabs" role="tablist" aria-label="Years" class="flex min-w-max lg:min-w-0"
                     @keydown.right.prevent="select(active + 1, true)" @keydown.left.prevent="select(active - 1, true)"
                     @keydown.home.prevent="select(0, true)" @keydown.end.prevent="select(years.length - 1, true)">
                    @foreach ($milestones as $i => $m)
                        <button type="button" role="tab" id="year-tab-{{ $i }}" aria-controls="year-panel-{{ $i }}"
                                aria-selected="{{ $i === $active ? 'true' : 'false' }}" tabindex="{{ $i === $active ? 0 : -1 }}"
                                :aria-selected="(active === {{ $i }}).toString()" :tabindex="active === {{ $i }} ? 0 : -1"
                                @click="select({{ $i }})"
                                @class([
                                    'min-w-[112px] flex-1 snap-start px-4 py-[18px] text-left text-[21px] font-bold tracking-[-.02em] transition-colors duration-150',
                                    'border-r border-rule' => ! $loop->last,
                                    'bg-brand-500 text-ink' => $i === $active,
                                    'text-faint hover:text-ink' => $i !== $active,
                                ])
                                :class="{ 'bg-brand-500 text-ink': active === {{ $i }}, 'text-faint hover:text-ink': active !== {{ $i }} }">
                            {{ $m->year }}
                        </button>
                    @endforeach
                </div>
            </div>

            @foreach ($milestones as $i => $m)
                <div role="tabpanel" id="year-panel-{{ $i }}" aria-labelledby="year-tab-{{ $i }}" tabindex="0"
                     x-show="active === {{ $i }}" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0"
                     @if ($i !== $active) style="display: none" @endif
                     class="grid items-center gap-10 pt-10 lg:grid-cols-2 lg:gap-16 lg:pt-14">
                    <div>
                        @if ($m->kicker)
                            <p class="label-mark">{{ $m->kicker }}</p>
                        @endif
                        <h3 class="mt-[18px] text-[clamp(1.625rem,1.1rem+1.8vw,2.625rem)] leading-[1.04] font-extrabold tracking-[-.035em] text-balance">{{ $m->title }}</h3>
                        @if ($m->body)
                            <p class="mt-5 max-w-[52ch] text-[17px] leading-[1.6] text-pretty text-body sm:text-[18px]">{{ $m->body }}</p>
                        @endif
                        @if ($m->meta)
                            <p class="mt-8 border-t border-rule pt-[22px] font-mono text-[11.5px] tracking-[.08em] text-mono uppercase">{{ $m->meta }}</p>
                        @endif
                    </div>
                    <div class="aspect-[4/3] bg-well">
                        @if ($m->image)
                            <img src="{{ ImageStore::url($m->image) }}" srcset="{{ ImageStore::srcset($m->image) }}" sizes="(min-width: 1024px) 640px, 100vw"
                                 alt="{{ $m->year }}: {{ $m->title }}" loading="lazy" class="size-full object-cover">
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    {{-- 4. Leadership --}}
    @if ($leaders->isNotEmpty())
        <section id="leadership" class="mt-20 border-y border-rule bg-paper-mid lg:mt-[108px]" aria-labelledby="leadership-title">
            <div class="site-container py-20 lg:py-24">
                <x-site.section-header :num="$num('leadership')" kicker="Leadership" title="Two brothers, two halves." id="leadership-title">
                    Between them they cover the whole job — one runs the floor, one runs the relationships. Both still answer the phone.
                </x-site.section-header>

                <div class="mt-10 grid gap-px bg-rule-mid md:grid-cols-2">
                    @foreach ($leaders as $person)
                        <article class="bg-paper">
                            <div class="aspect-[5/4] bg-rule">
                                @if ($person->photo)
                                    <img src="{{ ImageStore::url($person->photo) }}" srcset="{{ ImageStore::srcset($person->photo) }}" sizes="(min-width: 768px) 50vw, 100vw"
                                         alt="{{ $person->name }}" loading="lazy" class="size-full object-cover object-[50%_30%]">
                                @endif
                            </div>
                            <div class="px-6 pt-[34px] pb-[38px] sm:px-8">
                                @if ($person->role)
                                    <p class="label-mark">{{ $person->role }}</p>
                                @endif
                                <h3 class="mt-3.5 text-[clamp(1.625rem,1.3rem+1vw,2.125rem)] leading-none font-extrabold tracking-[-.03em]">{{ $person->name }}</h3>
                                @if ($person->bio)
                                    <div class="mt-4 text-[16.5px] leading-[1.6] text-pretty text-body">{!! $person->bio !!}</div>
                                @endif
                                @if ($person->quote)
                                    {{-- The only italic in the system: reserved for these quotes --}}
                                    <blockquote class="mt-[26px] border-t border-rule pt-5 text-base leading-[1.5] font-medium italic">“{{ $person->quote }}”</blockquote>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 5. Design India --}}
    <section id="design-india" @class(['bg-ink-deep text-white', 'mt-20 lg:mt-[108px]' => $leaders->isEmpty()]) aria-labelledby="di-title">
        <div class="site-container grid items-center gap-12 py-20 lg:grid-cols-[1.05fr_.95fr] lg:gap-20 lg:py-24">
            <div>
                <p class="kicker mb-[18px]">{{ $num('design-india') }} / Design India</p>
                <h2 id="di-title" class="text-[clamp(2rem,1.1rem+3.2vw,3.5rem)] leading-none font-extrabold tracking-[-.04em] text-balance">Powerstik is a brand of Design India.</h2>
                <p class="mt-6 max-w-[54ch] text-[17px] leading-[1.6] text-on-dark sm:text-[18px]">
                    Design India is the parent company — the design consultancy founded in {{ $since }}. Powerstik is the manufacturing brand it built to produce its own work: labels, cartons and corrugated packaging. One ownership, one quality standard, one accounts team.
                </p>
                <dl class="mt-11 grid gap-px bg-[#262626] sm:grid-cols-2">
                    <div class="bg-ink-deep px-6 py-7">
                        <dt class="text-[22px] font-bold tracking-[-.02em]">Design India</dt>
                        <dd class="mt-2 text-[15px] leading-[1.5] text-muted">Branding, packaging design, artwork and 3D mockups.</dd>
                    </div>
                    <div class="bg-ink-deep px-6 py-7">
                        <dt class="text-[22px] font-bold tracking-[-.02em]">Powerstik<sup class="text-[13px]">®</sup></dt>
                        <dd class="mt-2 text-[15px] leading-[1.5] text-muted">Offset, digital and flexo print; labels and corrugation.</dd>
                    </div>
                </dl>
            </div>
            <div class="aspect-square bg-[#161616]">
                @if ($media['design_india'])
                    <img src="{{ $media['design_india'] }}" alt="The design studio and the press hall side by side" loading="lazy" class="size-full object-cover">
                @endif
            </div>
        </div>
    </section>

    {{-- 6. Our people --}}
    <section id="people" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="people-title">
        <x-site.section-header :num="$num('people')" kicker="Our people" title="~{{ $people }} people, four teams." id="people-title">
            Nobody is a middleman. The person who designs your label works twenty metres from the press that prints it.
        </x-site.section-header>

        <ul class="mt-10 grid gap-px bg-rule sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($teams as $i => [$name, $body, $slot, $alt])
                <li class="bg-paper">
                    <div class="aspect-[4/3] bg-well">
                        @if ($media[$slot])
                            <img src="{{ $media[$slot] }}" alt="{{ $alt }}" loading="lazy" class="size-full object-cover">
                        @endif
                    </div>
                    <div class="px-[26px] pt-7 pb-8">
                        <p class="font-mono text-[11px] tracking-[.1em] text-faint uppercase">Team {{ sprintf('%02d', $i + 1) }}</p>
                        <h3 class="mt-3 text-[23px] leading-[1.1] font-bold tracking-[-.025em]">{{ $name }}</h3>
                        <p class="mt-2.5 text-[15px] leading-[1.55] text-body">{{ $body }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- 7. Quality --}}
    <section id="quality" class="mt-20 border-y border-rule bg-paper-mid lg:mt-[108px]" aria-labelledby="quality-title">
        <div class="site-container py-20 lg:py-24">
            <x-site.section-header :num="$num('quality')" kicker="Quality" title="Checked three times before it ships." id="quality-title">
                Incoming material, in-process control and pre-dispatch inspection. Reorders are matched against the retained sample, not a memory.
            </x-site.section-header>

            <div class="mt-12 grid items-start gap-12 lg:grid-cols-[1.1fr_.9fr] lg:gap-16">
                <ol class="grid gap-px bg-rule-mid md:grid-cols-3">
                    @foreach ($stages as $i => [$name, $body])
                        <li class="bg-paper px-[26px] pt-[30px] pb-[34px]">
                            <p class="label-mark">Stage {{ sprintf('%02d', $i + 1) }}</p>
                            <h3 class="mt-3 text-[22px] leading-[1.15] font-bold tracking-[-.025em]">{{ $name }}</h3>
                            <p class="mt-2.5 text-[15px] leading-[1.55] text-body">{{ $body }}</p>
                        </li>
                    @endforeach
                </ol>

                <div>
                    <h3 class="mb-[18px] font-mono text-[11px] tracking-[.12em] text-mono uppercase">Test list</h3>
                    <dl class="border-t border-rule-mid">
                        @foreach ($tests as [$test, $when])
                            <div class="flex items-baseline justify-between gap-5 border-b border-rule-mid py-[13px]">
                                <dt class="text-base font-medium">{{ $test }}</dt>
                                <dd class="shrink-0 text-right font-mono text-[11px] tracking-[.08em] text-mono uppercase">{{ $when }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if ($certifications)
                        <div class="mt-7 border border-rule-mid bg-paper px-6 py-[22px]">
                            <h3 class="font-mono text-[11px] tracking-[.12em] text-mono uppercase">Certifications</h3>
                            <ul class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-base font-semibold">
                                @foreach ($certifications as $cert)
                                    <li>{{ $cert }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- 8. Infrastructure --}}
    <section id="infrastructure" class="on-dark bg-ink text-white" aria-labelledby="infra-title">
        <div class="site-container py-20 lg:py-24">
            <header class="flex flex-col gap-6 border-b border-[#2e2e2e] pb-[26px] sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="kicker mb-[18px]">{{ $num('infrastructure') }} / Infrastructure</p>
                    <h2 id="infra-title" class="h-section">Walk the floor.</h2>
                </div>
                <a href="{{ url('/#machines') }}" class="self-start border-b-2 border-brand-500 pb-1 font-mono text-[12px] tracking-[.1em] text-white uppercase transition-colors hover:text-brand-500 sm:self-auto">See the machine park</a>
            </header>

            <div class="mt-10 grid gap-4 lg:grid-cols-[2fr_1fr]">
                {{-- Row-spanning item: sized by its span; min-w-0 keeps it from inflating the 2fr track --}}
                <div class="relative aspect-video min-w-0 bg-[#161616] lg:row-span-2 lg:aspect-auto lg:h-full">
                    @if ($media['plant_hall'])
                        <img src="{{ $media['plant_hall'] }}" alt="The main press hall" loading="lazy" class="size-full object-cover">
                    @endif
                    @if ($walkthrough)
                        <a href="{{ $walkthrough }}" target="_blank" rel="noopener" data-track="walkthrough"
                           class="absolute bottom-4 left-4 flex items-center gap-3 border border-[#3a3a3a] bg-[rgba(12,12,12,.82)] px-4 py-3 font-mono text-[11px] tracking-[.1em] whitespace-nowrap text-white uppercase transition-colors hover:border-white sm:bottom-[22px] sm:left-[22px]">
                            <span class="size-[9px] rounded-full bg-brand-500" aria-hidden="true"></span>
                            360° walkthrough · press hall
                        </a>
                    @endif
                </div>
                <div class="aspect-[4/3] min-w-0 bg-[#161616]">
                    @if ($media['plant_corrugation'])
                        <img src="{{ $media['plant_corrugation'] }}" alt="The corrugation line" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
                <div class="aspect-[4/3] min-w-0 bg-[#161616]">
                    @if ($media['plant_labels'])
                        <img src="{{ $media['plant_labels'] }}" alt="Label finishing and rewinding" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
            </div>

            <dl class="mt-4 grid grid-cols-2 gap-px bg-rule-dark lg:grid-cols-4">
                @foreach ($plantFacts as [$value, $label])
                    <div class="flex flex-col-reverse bg-ink px-[22px] py-[26px]">
                        <dt class="mt-2 font-mono text-[11px] tracking-[.08em] text-muted uppercase">{{ $label }}</dt>
                        <dd class="text-[clamp(1.25rem,1rem+.8vw,1.625rem)] leading-[1.1] font-extrabold tracking-[-.03em]">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 9. Sustainability: shown only once verified claims are entered in Settings → About page --}}
    @if ($sustainability)
        <section id="sustainability" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="sustain-title">
            <div class="grid items-center gap-12 lg:grid-cols-[.85fr_1.15fr] lg:gap-[72px]">
                <div class="aspect-[4/5] bg-well max-lg:order-last max-lg:aspect-[4/3]">
                    @if ($media['sustainability'])
                        <img src="{{ $media['sustainability'] }}" alt="Baled waste board ready to return to the mill" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
                <div>
                    <p class="kicker mb-[18px]">{{ $num('sustainability') }} / Sustainability</p>
                    <h2 id="sustain-title" class="text-[clamp(2rem,1.2rem+2.8vw,3.25rem)] leading-[1.02] font-extrabold tracking-[-.035em] text-balance">Paper is a recyclable material. We treat it like one.</h2>
                    <dl class="mt-10 grid gap-px bg-rule sm:grid-cols-2">
                        @foreach ($sustainability as [$name, $body])
                            <div class="bg-paper px-[22px] py-6">
                                <dt class="text-[18px] font-bold tracking-[-.02em]">{{ $name }}</dt>
                                @if ($body)
                                    <dd class="mt-1.5 text-[14.5px] leading-[1.5] text-body">{{ $body }}</dd>
                                @endif
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </section>
    @endif

    {{-- 10. Careers --}}
    <section id="careers" @class(['border-y border-rule bg-paper-mid', 'mt-20 lg:mt-[108px]' => (bool) $sustainability]) aria-labelledby="careers-title">
        <div class="site-container py-20 lg:py-24">
            <x-site.section-header :num="$num('careers')" kicker="Careers" title="Come make things that ship." id="careers-title">
                Designers here see their artwork on a pallet within the week. That is rare, and it is the reason people stay.
            </x-site.section-header>

            <div class="mt-11 grid items-start gap-12 lg:grid-cols-[1.25fr_.75fr] lg:gap-16">
                @if ($roles->isNotEmpty())
                    <ul class="border-t border-rule-mid">
                        @foreach ($roles as $role)
                            <li>
                                <a href="{{ $apply('Application: '.$role->title) }}" data-track="apply"
                                   class="group flex items-center justify-between gap-6 border-b border-rule-mid px-2 py-[26px] transition-colors hover:bg-paper">
                                    <span>
                                        <span class="block text-[clamp(1.25rem,1.1rem+.5vw,1.5625rem)] font-bold tracking-[-.025em]">{{ $role->title }}</span>
                                        <span class="mt-[7px] block font-mono text-[11px] tracking-[.08em] text-mono uppercase">
                                            {{ collect([$role->department, $role->location, JobOpening::EMPLOYMENT_TYPES[$role->employment_type] ?? null])->filter()->join(' · ') }}
                                        </span>
                                    </span>
                                    <span class="font-mono text-[13px] text-ink" aria-hidden="true">→</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="border-y border-rule-mid py-[26px] text-[clamp(1.25rem,1.1rem+.5vw,1.5625rem)] font-bold tracking-[-.025em]">
                        No open roles right now — send your portfolio anyway.
                    </p>
                @endif

                <div>
                    <div class="aspect-square bg-rule max-lg:aspect-[4/3]">
                        @if ($media['careers'])
                            <img src="{{ $media['careers'] }}" alt="The Powerstik team at work" loading="lazy" class="size-full object-cover">
                        @endif
                    </div>
                    <a href="{{ $apply('Portfolio') }}" data-track="portfolio"
                       class="mt-4 flex items-center justify-between gap-4 bg-ink px-6 py-5 text-base font-bold text-white transition-colors hover:bg-brand-500 hover:text-ink">
                        Send us your portfolio <span class="font-mono text-[13px]" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 11. Final CTA band --}}
    <x-site.cta-band id="cta-title" body="Size, quantity, sheet or roll, and your artwork. A named coordinator replies within one working day.">
        <x-slot:title>Now you know us.<br> Send us a job.</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="url('/resources/downloads')" data-track="profile_cta">Download company profile</x-site.cta-link>
        <x-site.cta-link :href="$whatsapp ?? url('/contact')" :external="(bool) $whatsapp" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
    </x-site.cta-band>
</x-layouts.site>
