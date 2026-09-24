@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $quote = url(config('site.quote_url'));
    $countriesTitle = ([1 => 'One country', 'Two countries', 'Three countries', 'Four countries', 'Five countries', 'Six countries', 'Seven countries', 'Eight countries', 'Nine countries', 'Ten countries', 'Eleven countries', 'Twelve countries'][$countries->count()] ?? $countries->count().' countries').', one standard.';
    $whatsapp = Site::whatsappUrl();

    // Fixed marketing copy from the design. CMS-driven sections read from the database.
    $tiles = [
        ['01', 'Design & branding', 'Logo, packaging and label design with 3D mockups before a plate is made. The studio that started it all.', 'In-house team', '/capabilities/design-branding'],
        ['02', 'Label printing', 'Sheet and roll labels — core size and winding direction set for your applicator. Offset, digital or flexo.', 'MOQ '.preg_replace('/\D/', '', Site::setting('moq', '50')).' units', '/capabilities/label-printing'],
        ['03', 'Corrugated packaging', 'Mono cartons through 3, 5, 7 and 9-ply. Box styles, flute selection and print options in one conversation.', 'Up to 9-ply', '/capabilities/corrugated-packaging'],
        ['04', 'Print technologies', 'We tell you when offset beats digital and when flexo wins — then run it on our own press.', 'Offset / digital / flexo', '/capabilities/printing-technologies'],
    ];

    $steps = [
        ['Brief', 'Spec, quantity, deadline. One coordinator assigned.'],
        ['Design', 'Artwork or dieline from our studio, mockup included.'],
        ['Approval', 'Digital proof signed off. The 2–5 day clock starts here.'],
        ['Print', 'Heidelberg, Canon or Mark Andy, whichever suits the run.'],
        ['QC / PDI', 'Incoming, in-process and pre-dispatch inspection.'],
        ['Dispatch', 'Packed, documented and out — bulk orders included.'],
    ];

    // kind, name, spec, and the maker's cutout at public/media/machines/{file}.webp.
    // One card per press, so each carries its own picture; the tinted well shows if a file is missing.
    $machines = [
        ['Offset · Germany', 'Two Heidelberg Speedmasters', 'The five-colour SM-74 for cartons and sheet labels, and the large-format CD-102 for long runs and heavy stock.', 'heidelberg'],
        ['Flexo · USA', 'Mark Andy E5', 'Roll label press — applicator-ready reels, core size and winding direction to spec.', 'mark-andy'],
        ['Digital', 'Digital press', 'Short runs, samples and variable data, without a plate or a minimum.', 'digital'],
        ['Corrugation', 'Semi-automatic carton printer', 'Print, slot and die-cut in line: mono cartons through 3, 5, 7 and 9-ply, in-house.', 'corrugation'],
    ];
@endphp

<x-layouts.site>
    {{-- 3. Hero --}}
    <section class="on-dark relative flex h-[88svh] max-h-[760px] min-h-[600px] items-end overflow-hidden bg-ink text-white lg:h-[760px]" aria-labelledby="hero-title">
        @if ($media['hero_video'])
            <video class="absolute inset-0 size-full object-cover" autoplay muted loop playsinline preload="none"
                   @if ($media['hero_poster']) poster="{{ $media['hero_poster'] }}" @endif data-hero-video aria-hidden="true">
                <source src="{{ $media['hero_video'] }}" type="video/mp4">
            </video>
        @elseif ($media['hero_poster'])
            <img src="{{ $media['hero_poster'] }}" alt="" class="absolute inset-0 size-full object-cover" fetchpriority="high">
        @endif
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(10,10,10,.92)_0%,rgba(10,10,10,.78)_46%,rgba(10,10,10,.18)_100%)] max-md:bg-[linear-gradient(0deg,rgba(10,10,10,.94)_0%,rgba(10,10,10,.8)_60%,rgba(10,10,10,.4)_100%)]" aria-hidden="true"></div>

        <div class="site-container-wide relative pb-14 lg:pb-[88px]">
            <p class="mb-[26px] flex items-center gap-3.5 font-mono text-[11px] tracking-[.14em] uppercase sm:text-[12px]">
                <span class="h-0.5 w-[46px] shrink-0 bg-brand-500" aria-hidden="true"></span>
                Design studio, est. {{ Site::setting('since_year', '2002') }} · Print & packaging plant, Haryana
            </p>
            <h1 id="hero-title" class="max-w-[20ch] text-[clamp(2.5rem,1.2rem+5.2vw,5.5rem)] leading-[.94] font-extrabold tracking-[-.035em] text-balance">
                Designed like a studio.<br>
                <span class="text-brand-500">Delivered like a factory.</span>
            </h1>
            <p class="mt-7 max-w-[56ch] text-[17px] leading-[1.5] text-on-dark-soft sm:text-[19px]">
                We began as a small design setup and built the plant to match — Heidelberg offset, flexo roll labels and a full corrugation line, all fed by our own designers. Labels and packaging from {{ preg_replace('/\D/', '', Site::setting('moq', '50')) }} units to any volume, dispatched in {{ $counters[4]['value'] }} days.
            </p>
            <div class="mt-10 flex flex-wrap gap-3.5">
                <a href="{{ $quote }}" data-track="quote_hero" class="btn btn-brand px-8 py-[18px] text-base hover:bg-white hover:text-ink">Get a quote</a>
                <a href="#capabilities" class="btn btn-ghost-dark px-[30px] py-[17px] text-base">Explore capabilities</a>
            </div>
        </div>
    </section>

    {{-- 4. Proof strip --}}
    <section class="border-t border-rule-dark bg-ink text-white" aria-label="Powerstik in numbers">
        <div class="site-container-wide">
        <dl class="grid grid-cols-2 gap-px bg-rule-dark sm:grid-cols-3 lg:grid-cols-6" data-counters>
            @foreach ($counters as $c)
                <div class="flex flex-col-reverse bg-ink px-4 py-8 sm:px-6 lg:pt-11 lg:pb-[46px]">
                    <dt class="mt-2.5 font-mono text-[11px] tracking-[.1em] text-muted uppercase sm:text-[11.5px]">{{ $c['label'] }}</dt>
                    <dd class="text-[clamp(2rem,1.4rem+1.6vw,2.875rem)] leading-none font-extrabold tracking-[-.04em] tabular-nums"
                        @unless ($c['static'] ?? false) data-count data-from="{{ $c['from'] }}" @endunless>{{ $c['value'] }}</dd>
                </div>
            @endforeach
        </dl>
        </div>
    </section>

    {{-- 5. Client wall. Names are set as text until each logo is cleared for use. --}}
    @if ($clients->isNotEmpty())
        <section class="border-b border-rule" aria-labelledby="clients-title">
            <h2 id="clients-title" class="site-container-wide pt-[34px] pb-3 font-mono text-[11.5px] tracking-[.12em] text-mono uppercase">Trusted by {{ Site::setting('clients', '1,500+') }} manufacturers</h2>
            <div class="ps-marquee-viewport overflow-hidden pb-9 [mask-image:linear-gradient(90deg,transparent,#000_8%,#000_92%,transparent)]">
                <div class="ps-marquee flex w-max">
                    @foreach ([false, true] as $duplicate)
                        <ul class="flex" @if ($duplicate) aria-hidden="true" @endif>
                            @foreach ($clients as $client)
                                <li class="flex h-12 shrink-0 items-center px-[34px]">
                                    @if ($client->show_logo && $client->logo)
                                        <img src="{{ ImageStore::url($client->logo) }}" alt="{{ $duplicate ? '' : $client->name }}" loading="lazy" class="h-9 w-auto opacity-70 grayscale">
                                    @else
                                        <span class="text-[26px] font-bold tracking-[-.035em] whitespace-nowrap text-logo sm:text-[30px]">{{ $client->name }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 6. What we do --}}
    <section id="capabilities" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="capabilities-title">
        <x-site.section-header num="01" kicker="What we do" title="Four disciplines, one roof." id="capabilities-title">
            Artwork, plates, print and box never change hands between vendors. That is why a brief on Monday ships as a pallet on Friday.
        </x-site.section-header>
        <div class="grid gap-px bg-rule sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($tiles as [$num, $title, $body, $spec, $href])
                <a href="{{ url($href) }}" class="flex min-h-[280px] flex-col bg-paper px-8 pt-10 pb-[34px] transition-colors hover:bg-paper-mid lg:min-h-[320px]">
                    <span class="font-mono text-[11.5px] text-faint">{{ $num }}</span>
                    <h3 class="mt-5 text-[27px] leading-[1.1] font-bold tracking-[-.025em]">{{ $title }}</h3>
                    <p class="mt-3.5 text-[15.5px] leading-[1.55] text-body">{{ $body }}</p>
                    <span class="label-mark mt-auto pt-[26px]">{{ $spec }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- 7. Battery labels spotlight --}}
    <section class="on-dark mt-20 bg-ink-deep text-white lg:mt-[108px]" aria-labelledby="battery-title">
        <div class="site-container grid items-center gap-14 py-20 lg:grid-cols-[1.05fr_.95fr] lg:gap-20 lg:py-[100px]">
            <div class="lg:order-1 max-lg:order-2">
                <p class="mb-[30px] inline-block border border-brand-500 px-3.5 py-2 font-mono text-[11px] tracking-[.14em] text-brand-500 uppercase">Flagship · India’s most trusted battery sticker</p>
                <h2 id="battery-title" class="text-[clamp(2.25rem,1.2rem+3.4vw,3.875rem)] leading-[.98] font-extrabold tracking-[-.04em] text-balance">Paper that survives<br> the acid and the heat.</h2>
                <p class="mt-6 max-w-[54ch] text-[17px] leading-[1.55] text-on-dark sm:text-[18px]">Twenty-five years of core experience in battery label design and printing sit behind it. Our proprietary battery label paper was developed in-house for Indian and export lead-acid production lines: electrolyte splash, bonnet heat and high-speed applicators. Main, warning, terminal and warranty labels in sheet or roll, plus the corrugated box around them.</p>
                <dl class="mt-11 grid grid-cols-3 gap-px bg-[#262626]">
                    @foreach ([['Acid', 'No lift, no bleed'], ['Heat', 'Bonnet-temperature stable'], ['Grip', 'Applicator-ready adhesion']] as [$word, $caption])
                        <div class="flex flex-col-reverse bg-ink-deep px-3 py-5 sm:px-5 sm:py-6">
                            <dt class="mt-1.5 font-mono text-[10px] leading-snug text-muted sm:text-[11px]">{{ $caption }}</dt>
                            <dd class="text-[22px] font-extrabold tracking-[-.03em] sm:text-[30px]">{{ $word }}</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="mt-10 flex flex-wrap gap-3.5">
                    <a href="{{ url(config('site.sample_url')) }}" data-track="sample" class="btn btn-brand px-7 py-4 text-[15.5px] hover:bg-white hover:text-ink">Request a sample</a>
                    <a href="{{ url('/resources/downloads') }}" data-track="datasheet" class="btn border border-[#3a3a3a] px-7 py-4 text-[15.5px] font-semibold text-white hover:border-white">Download data sheet</a>
                </div>
            </div>
            <div class="relative max-lg:order-1 lg:order-2 max-lg:mx-auto max-lg:w-full max-lg:max-w-md">
                <div class="aspect-[4/5] bg-[#161616]">
                    @if ($media['battery'])
                        <img src="{{ $media['battery'] }}" alt="Powerstik battery label on a lead-acid battery" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
                {{-- Acid-test badge: one droplet ring, pulsing --}}
                <div class="absolute -bottom-[22px] -left-3 grid size-[120px] place-items-center border border-brand-500 bg-ink-deep/86 sm:-left-[22px] sm:size-[132px]" aria-hidden="true">
                    <span class="ps-drop absolute size-[54px] rounded-full border border-brand-500"></span>
                    <span class="relative text-center font-mono text-[10.5px] leading-[1.6] tracking-[.1em] uppercase">Acid<br>test<br>passed</span>
                </div>
            </div>

            {{-- Proof band. The client's own photograph of a sheet under test, so the three
                 words above are evidenced rather than asserted. Hidden until the file exists. --}}
            @if ($media['acid_test'])
                <div class="order-3 mt-2 border-t border-[#2b2b2b] pt-11 lg:col-span-2 lg:mt-5 lg:pt-12">
                    <div class="flex flex-col gap-8 sm:flex-row sm:items-center sm:gap-10 lg:gap-14">
                        <img src="{{ $media['acid_test'] }}" width="1400" height="933" loading="lazy"
                             alt="A sheet of Powerstik battery label paper in the test tray, marked “Heat Test” in pencil and dated, under sulphuric acid, with the stain climbing from the lower edge"
                             class="h-auto w-full max-w-[420px] shrink-0 sm:w-[46%]">
                        <div>
                            <p class="label-mark">From the test bench</p>
                            <p class="mt-4 max-w-[52ch] text-[16px] leading-[1.55] text-on-dark">Full-strength battery electrolyte, a day at bonnet temperature. A sheet goes into the tray dated and marked up by hand, and comes out without lifting or bleeding. Not a render and not a sample printed for a website.</p>
                            <dl class="mt-7 max-w-[360px] font-mono text-[11.5px] tracking-[.08em] uppercase">
                                @foreach ([['Electrolyte', 'Sulphuric acid, 1.280 sp. gr.'], ['Temperature', '60 °C'], ['Duration', '24 hours']] as [$term, $value])
                                    <div class="flex items-baseline justify-between gap-5 border-t border-[#2b2b2b] py-2.5">
                                        <dt class="text-muted">{{ $term }}</dt>
                                        <dd class="text-right text-white">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- 8. How we work --}}
    <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="process-title">
        <x-site.section-header num="02" kicker="How we work" title="Brief to pallet in {{ $counters[4]['value'] }} days." id="process-title">
            One coordinator owns your job end to end. Bulk orders included — the clock starts at artwork approval.
        </x-site.section-header>
        <ol class="mt-12 grid gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            @foreach ($steps as $i => [$title, $body])
                <li class="pr-[22px]">
                    <div class="mb-[22px] flex items-center gap-2.5" aria-hidden="true">
                        <span class="size-[11px] shrink-0 rounded-full border border-ink bg-brand-500"></span>
                        <span class="h-px flex-1 bg-rule-mid"></span>
                    </div>
                    <p class="font-mono text-[11px] tracking-[.1em] text-faint uppercase">Step {{ sprintf('%02d', $i + 1) }}</p>
                    <h3 class="mt-2 text-[22px] font-bold tracking-[-.02em]">{{ $title }}</h3>
                    <p class="mt-2 text-[14.5px] leading-[1.5] text-body">{{ $body }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- 9. Industries --}}
    @if ($industries->isNotEmpty())
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="industries-title">
            <x-site.section-header num="03" kicker="Industries" title="Nine sectors, nine rulebooks." id="industries-title">
                Pharma legibility, food-safe inks, QSR grease resistance — we know the constraint before you name it.
            </x-site.section-header>
            <ul class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($industries as $industry)
                    <li>
                        <a href="{{ url('/industries/'.$industry->slug) }}" class="group block">
                            <div class="aspect-[4/3] overflow-hidden bg-well">
                                @if ($industry->card_image)
                                    <img src="{{ ImageStore::url($industry->card_image) }}" srcset="{{ ImageStore::srcset($industry->card_image) }}"
                                         sizes="(min-width: 1024px) 440px, (min-width: 640px) 50vw, 100vw" alt="{{ $industry->name }} packaging by Powerstik"
                                         loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
                                @endif
                            </div>
                            <div class="mt-3.5 flex items-baseline justify-between gap-4 border-t border-ink px-0.5 pt-4">
                                <h3 class="-mx-1 px-1 text-[22px] font-bold tracking-[-.02em] transition-colors group-hover:bg-brand-500">{{ $industry->name }}</h3>
                                @if ($industry->note)
                                    <span class="text-right font-mono text-[11px] tracking-[.08em] text-mono uppercase">{{ $industry->note }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 10. Machine park teaser --}}
    <section id="machines" class="mt-20 border-y border-rule bg-paper-mid lg:mt-[108px]" aria-labelledby="machines-title">
        <div class="site-container grid items-center gap-12 py-20 lg:grid-cols-[.82fr_1.18fr] lg:gap-[72px] lg:py-24">
            <div>
                <p class="kicker mb-[18px]">04 / Machine park</p>
                <h2 id="machines-title" class="text-[clamp(2rem,1.2rem+2.6vw,3.25rem)] leading-[1.02] font-extrabold tracking-[-.035em] text-balance">We bought the machines so you wouldn’t have to chase them.</h2>
                <p class="mt-6 text-[17px] leading-[1.55] text-body">Offset, digital, flexo and corrugation on one floor, ~{{ preg_replace('/\D/', '', Site::setting('people', '200')) }} people running them and {{ Site::setting('labels_per_day', '5,00,000') }} labels a day off the presses. No job is subcontracted out of sight.</p>
                <a href="{{ url('/about/infrastructure') }}" class="link-underline mt-[30px] text-[15.5px]">Take the plant tour</a>
            </div>
            <ul class="grid gap-px bg-rule-mid sm:grid-cols-2">
                @foreach ($machines as [$kind, $name, $spec, $file])
                    @php($cutout = is_file(public_path("media/machines/{$file}.webp")) ? asset("media/machines/{$file}.webp") : null)
                    <li class="flex flex-col bg-paper px-7 py-[26px]">
                        <div class="mb-[22px] grid h-[92px] place-items-center @unless ($cutout) bg-well @endunless">
                            @if ($cutout)
                                <img src="{{ $cutout }}" alt="{{ $name }}" width="1200" height="420" loading="lazy" class="max-h-full w-auto max-w-full object-contain">
                            @endif
                        </div>
                        <p class="label-mark">{{ $kind }}</p>
                        <h3 class="mt-3 text-[22px] leading-[1.15] font-bold tracking-[-.025em]">{{ $name }}</h3>
                        <p class="mt-2 text-[14.5px] leading-[1.5] text-body">{{ $spec }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- 11. Global reach --}}
    @if ($countries->isNotEmpty())
        <section class="on-dark bg-ink text-white" aria-labelledby="global-title">
            <div class="site-container py-20 lg:py-[100px]">
                <x-site.section-header num="05" kicker="Global reach" :title="$countriesTitle" :dark="true" id="global-title">
                    Export documentation, Incoterms, port-ready packing and repeat-order colour consistency, handled by the same coordinator.
                </x-site.section-header>
                <div class="mt-14 grid items-center gap-16 lg:grid-cols-[1.15fr_.85fr]">
                    {{-- The client's export map (recoloured for the ink ground). Hidden on narrow screens. --}}
                    @if ($media['export_map'])
                        <img src="{{ $media['export_map'] }}" width="1400" height="787" loading="lazy"
                             class="hidden h-auto w-full min-[900px]:block"
                             alt="Powerstik exports from Haryana, India to {{ $countries->pluck('name')->join(', ', ' and ') }}">
                    @else
                    {{-- Fallback until the artwork is in place: a hub with arcs fanning out. --}}
                    <svg viewBox="0 0 620 420" class="hidden h-auto w-full overflow-visible min-[900px]:block" data-inview role="img" aria-label="Export routes from Haryana, India to {{ $countries->pluck('name')->join(', ', ' and ') }}">
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

                    <div>
                        <ul class="grid grid-cols-2 gap-px bg-rule-dark">
                            @foreach ($countries as $country)
                                <li class="bg-ink px-[18px] py-5">
                                    <p class="text-[19px] font-bold tracking-[-.02em]">{{ $country->name }}</p>
                                    @if ($country->note)
                                        <p class="mt-1.5 font-mono text-[10.5px] tracking-[.08em] text-muted uppercase">{{ $country->note }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ url(config('site.international_url')) }}" data-track="international" class="btn mt-[34px] bg-white px-7 py-4 text-[15.5px] font-bold text-ink hover:bg-brand-500">International enquiry</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 12. Featured work --}}
    @if ($cases->isNotEmpty())
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="work-title">
            <x-site.section-header num="06" kicker="Featured work" title="Problem, press, result." id="work-title">
                <a href="{{ url('/work') }}" class="inline-block border-b-2 border-brand-500 pb-1 font-mono text-[12px] tracking-[.1em] whitespace-nowrap text-ink uppercase transition-colors hover:border-ink">See full portfolio</a>
            </x-site.section-header>
            <ul class="mt-10 grid gap-7 md:grid-cols-3">
                @foreach ($cases as $case)
                    <li>
                        <a href="{{ url('/work/'.$case->slug) }}" class="flex h-full flex-col bg-white transition-colors hover:bg-paper-mid">
                            <div class="aspect-[3/2] bg-well">
                                @if ($case->cover_image)
                                    <img src="{{ ImageStore::url($case->cover_image) }}" srcset="{{ ImageStore::srcset($case->cover_image) }}"
                                         sizes="(min-width: 768px) 33vw, 100vw" alt="{{ $case->title }}" loading="lazy" class="size-full object-cover">
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col px-[26px] pt-[26px] pb-[30px]">
                                @if ($case->card_tag)
                                    <p class="label-mark">{{ $case->card_tag }}</p>
                                @endif
                                <h3 class="mt-3.5 text-[25px] leading-[1.15] font-bold tracking-[-.025em]">{{ $case->title }}</h3>
                                @if ($case->summary)
                                    <p class="mt-3 text-[15px] leading-[1.55] text-body">{{ $case->summary }}</p>
                                @endif
                                @if ($case->result_headline)
                                    <div class="mt-auto pt-5">
                                        <p class="border-t border-rule pt-4 text-[14.5px] font-bold">{{ $case->result_headline }}</p>
                                    </div>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 13. Founders' note --}}
    @if ($founders->isNotEmpty())
        <section class="mt-20 border-t border-rule bg-paper-mid lg:mt-[108px]" aria-labelledby="founders-title">
            <div class="site-container grid items-center gap-12 py-20 lg:grid-cols-[.9fr_1.1fr] lg:gap-[72px] lg:py-24">
                <div class="aspect-[4/3] bg-rule">
                    @if ($media['founders'])
                        <img src="{{ $media['founders'] }}" alt="{{ $founders->pluck('name')->join(' and ') }} on the Powerstik shop floor" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
                <div>
                    <p id="founders-title" class="kicker mb-[18px]">07 / A note from the founders</p>
                    @if ($foundersQuote)
                        <blockquote class="text-[clamp(1.25rem,1rem+.8vw,1.75rem)] leading-[1.38] font-medium tracking-[-.02em] text-pretty">“{{ $foundersQuote }}”</blockquote>
                    @endif
                    <div class="mt-9 flex flex-wrap gap-x-12 gap-y-5 border-t border-rule-mid pt-[26px]">
                        @foreach ($founders as $person)
                            <div>
                                <p class="text-[17px] font-bold">{{ $person->name }}</p>
                                @if ($person->role)
                                    <p class="mt-[5px] font-mono text-[11px] tracking-[.08em] text-mono uppercase">{{ $person->role }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 14. Testimonials (continues the founders' band) --}}
    @if ($testimonials->isNotEmpty())
        <section @class(['border-b border-rule bg-paper-mid', 'mt-20 lg:mt-[108px] pt-20' => $founders->isEmpty()]) aria-label="What clients say">
            <div class="site-container pb-20 lg:pb-24">
                <ul class="grid gap-px bg-rule-mid md:grid-cols-3">
                    @foreach ($testimonials as $t)
                        <li class="flex flex-col bg-paper px-8 pt-[38px] pb-[34px]">
                            <span class="grid size-9 place-items-center bg-brand-500 text-[30px] leading-none font-extrabold text-ink" aria-hidden="true">“</span>
                            <blockquote class="mt-3.5 text-[17.5px] leading-[1.5] tracking-[-.01em]">{{ $t->quote }}</blockquote>
                            <div class="mt-auto flex items-center gap-3.5 pt-7">
                                <div class="size-11 shrink-0 overflow-hidden rounded-full bg-rule">
                                    @if ($t->photo)
                                        <img src="{{ ImageStore::url($t->photo) }}" alt="" loading="lazy" class="size-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="text-[14.5px] font-bold">{{ $t->person }}</p>
                                    @if ($t->role || $t->company)
                                        <p class="mt-0.5 font-mono text-[10.5px] tracking-[.08em] text-mono uppercase">{{ collect([$t->role, $t->company])->filter()->join(', ') }}</p>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- 15. Final CTA band --}}
    <x-site.cta-band id="cta-title" body="Send size, quantity, sheet or roll and your artwork. A coordinator — a person, named, reachable — replies within one working day."
                     :class="$founders->isEmpty() && $testimonials->isEmpty() ? 'mt-20 lg:mt-[108px]' : ''">
        <x-slot:title>Have a job?<br> Get a quote in 24 hours.</x-slot:title>
        <x-site.cta-link :href="$quote" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="$whatsapp ?? url('/contact')" :external="(bool) $whatsapp" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        <x-site.cta-link :href="url(config('site.callback_url'))" data-track="callback">Request a callback</x-site.cta-link>
    </x-site.cta-band>
</x-layouts.site>
