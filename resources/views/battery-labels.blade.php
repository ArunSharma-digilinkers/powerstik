@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $since = (int) Site::setting('since_year', '2002');
    $dispatch = Site::setting('dispatch_days', '2–5 days');
    $labelsPerDay = Site::setting('labels_per_day', '5,00,000');
    $whatsapp = Site::whatsappUrl();

    // The embedded sample form shares its fields with /request-a-sample
    $bagErrors = $errors->getBag('sample');
    $prev = fn (string $key, $default = null) => $bagErrors->any() ? old($key, $default) : $default;

    // Section numbers run in order over the sections actually shown
    $shown = array_keys(array_filter([
        'paper' => true, 'types' => true, 'formats' => true, 'packaging' => true,
        'clients' => $clients->isNotEmpty(), 'work' => $cases->isNotEmpty(), 'sample' => true,
    ]));
    $num = fn (string $section) => sprintf('%02d', array_search($section, $shown) + 1);

    // From the brochure and the home flagship section — the client's own claims.
    $proof = [
        ['25 years', 'Core experience in battery labels'],
        [$labelsPerDay, 'Labels a day across the floor'],
        [$countries->count(), 'Export markets'],
        [trim(preg_replace('/\s*days?$/i', '', $dispatch)).' days', 'Dispatch after approval'],
    ];

    $tests = [['Electrolyte', 'Sulphuric acid, 1.280 sp. gr.'], ['Temperature', '60 °C'], ['Duration', '24 hours']];

    // The four labels on a battery. Home already names them; this is what each one has to survive.
    $types = [
        ['Main / brand label', 'The face of the battery: brand, model, capacity, the artwork a buyer actually looks at. Full colour, and it has to still look new after a year in a boot.', 'Full-colour offset or digital'],
        ['Warning label', 'Handling, charging and disposal marks. Legibility is the entire job — it has to survive what the battery does and still be readable.', 'High-contrast, acid-stable'],
        ['Terminal label', 'Small, close to the posts, and first in line for splash and heat. The one that tells you whether a battery paper is any good.', 'Worst-case placement'],
        ['Warranty & date label', 'Serial, batch and date of sale. It has to stay stuck and stay readable for as long as the claim is open.', 'Variable data, sheet or roll'],
    ];

    $rollSpecs = [
        ['Core size', '1, 1.5 or 3 inch'],
        ['Winding direction', 'Out 1–4 or in 1–4'],
        ['Labels per roll', 'Whatever your applicator takes'],
        ['Maximum roll diameter', 'Tell us and we wind to it'],
    ];
@endphp

<x-layouts.site title="Battery labels"
                description="Our proprietary battery label paper, developed in-house for lead-acid production lines: full-strength electrolyte at 60 °C for 24 hours, without lifting or bleeding. Main, warning, terminal and warranty labels in sheet or roll.">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="battery-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Battery labels' => url('/battery-labels')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="mb-[26px] inline-block border border-brand-500 px-3.5 py-2 font-mono text-[11px] tracking-[.14em] text-brand-500 uppercase">Flagship · India’s most trusted battery sticker</p>
                    <h1 id="battery-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Made for acid,<br> heat and the<br> applicator.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Twenty-five years of core experience in battery label design and printing sit behind one proprietary paper, developed in-house for Indian and export lead-acid lines. Everything on this page is what it has to survive.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. Proof strip --}}
    <section class="border-t border-rule-dark bg-ink text-white" aria-label="Battery labels in numbers">
        <div class="site-container">
            <dl class="grid grid-cols-2 gap-px bg-rule-dark lg:grid-cols-4">
                @foreach ($proof as [$value, $label])
                    <div class="flex flex-col-reverse bg-ink px-4 py-8 sm:px-6 lg:pt-10 lg:pb-[42px]">
                        <dt class="mt-2.5 font-mono text-[11px] leading-snug tracking-[.1em] text-muted uppercase sm:text-[11.5px]">{{ $label }}</dt>
                        <dd class="text-[clamp(1.75rem,1.3rem+1.2vw,2.5rem)] leading-none font-extrabold tracking-[-.04em] tabular-nums">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 3. The paper --}}
    <section id="paper" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="paper-title">
        <x-site.section-header :num="$num('paper')" kicker="The paper" title="It starts with the stock." id="paper-title">
            Our battery label paper was developed in-house, not bought in. Electrolyte splash, bonnet heat and a high-speed applicator are the three things it has to take.
        </x-site.section-header>

        <div class="grid gap-12 pt-12 lg:grid-cols-[.85fr_1.15fr] lg:items-center lg:gap-16">
            {{-- A fixed height rather than an aspect ratio: at 4:5 this column runs
                 taller than the copy beside it and takes over the section. --}}
            <div class="grid h-[340px] place-items-center bg-paper-mid p-8 sm:h-[420px] lg:h-[440px]">
                @if ($media['battery'])
                    {{-- A cutout product shot on white, so it is contained on a tint
                         rather than cropped to fill, the same as the machine cards. --}}
                    <img src="{{ $media['battery'] }}" alt="A roll of printed battery labels on Powerstik battery paper" width="1200" height="1500" loading="lazy" class="max-h-full w-auto max-w-full object-contain mix-blend-multiply">
                @endif
            </div>

            <div>
                <dl class="grid grid-cols-3 gap-px bg-rule-mid">
                    @foreach ([['Acid', 'No lift, no bleed'], ['Heat', 'Bonnet-temperature stable'], ['Grip', 'Applicator-ready adhesion']] as [$word, $caption])
                        <div class="flex flex-col-reverse bg-paper px-3 py-5 sm:px-5 sm:py-6">
                            <dt class="mt-1.5 font-mono text-[10px] leading-snug text-mono sm:text-[11px]">{{ $caption }}</dt>
                            <dd class="text-[22px] font-extrabold tracking-[-.03em] sm:text-[30px]">{{ $word }}</dd>
                        </div>
                    @endforeach
                </dl>
                <p class="mt-8 text-[17px] leading-[1.55] text-body">
                    Those three words are the whole specification, and they are the three we test for. A battery label does not fail slowly — it lifts at a corner, the ink bleeds, or it never sticks properly in the first place. Each of those is a return, and a return on a battery is never just the label.
                </p>
                <p class="mt-5 text-[17px] leading-[1.55] text-body">
                    The stock is ours, so when a line changes — a hotter bonnet, a faster applicator, a different surface — we can change the paper rather than apologise for it.
                </p>
                <p class="mt-8"><a href="#sample" data-track="sample_anchor" class="link-underline">Ask for a sheet and test it yourself</a></p>
            </div>
        </div>

        {{-- Proof band: the client's own test-bench photograph, with the protocol beside it --}}
        @if ($media['acid_test'])
            <div class="mt-14 border-t border-rule pt-12 lg:mt-16">
                <div class="flex flex-col gap-8 sm:flex-row sm:items-center sm:gap-10 lg:gap-14">
                    <img src="{{ $media['acid_test'] }}" width="1400" height="933" loading="lazy"
                         alt="A sheet of Powerstik battery label paper in the test tray, marked “Heat Test” in pencil and dated, under sulphuric acid, with the stain climbing from the lower edge"
                         class="h-auto w-full max-w-[420px] shrink-0 sm:w-[46%]">
                    <div>
                        <p class="label-mark">From the test bench</p>
                        <p class="mt-4 max-w-[52ch] text-[16px] leading-[1.55] text-body">
                            A sheet goes into the tray dated and marked up by hand, and comes out without lifting or bleeding. This is the photograph, not a render and not a sample printed for a website.
                        </p>
                        <dl class="mt-7 max-w-[380px] font-mono text-[11.5px] tracking-[.08em] uppercase">
                            @foreach ($tests as [$term, $value])
                                <div class="flex items-baseline justify-between gap-5 border-t border-rule py-2.5">
                                    <dt class="text-mono">{{ $term }}</dt>
                                    <dd class="text-right font-medium text-ink">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- 4. Label types --}}
    <section id="types" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="types-title">
        <x-site.section-header :num="$num('types')" kicker="What goes on a battery" title="Four labels, four jobs." id="types-title">
            They are printed together, on the same stock, in one pass of the same approval. Nobody has to chase four vendors for one battery.
        </x-site.section-header>

        <div class="grid gap-px bg-rule sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($types as $i => [$title, $body, $spec])
                <div class="flex min-h-[280px] flex-col bg-paper px-7 pt-9 pb-8 lg:min-h-[320px] lg:px-8 lg:pt-10">
                    <span class="font-mono text-[11.5px] text-faint">{{ sprintf('%02d', $i + 1) }}</span>
                    <h3 class="mt-5 text-[23px] leading-[1.1] font-bold tracking-[-.025em]">{{ $title }}</h3>
                    <p class="mt-3.5 text-[15.5px] leading-[1.55] text-body">{{ $body }}</p>
                    <span class="label-mark mt-auto pt-[26px]">{{ $spec }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 5. Sheet or roll --}}
    <section id="formats" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="formats-title">
        <x-site.section-header :num="$num('formats')" kicker="Sheet or roll" title="However your line takes them." id="formats-title">
            Hand-applied or on an applicator, the paper is the same. What changes is how it is cut, counted and wound.
        </x-site.section-header>

        <div class="grid gap-px bg-rule pt-12 sm:grid-cols-2 lg:gap-0 lg:bg-transparent lg:pt-12">
            <div class="bg-paper lg:pr-12">
                <div class="aspect-[16/10] bg-well">
                    @if ($media['sheet'])
                        <img src="{{ $media['sheet'] }}" alt="Battery labels printed up in sheets" width="1200" height="750" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
                <h3 class="mt-7 text-[26px] leading-[1.1] font-extrabold tracking-[-.03em]">Sheets</h3>
                <p class="mt-3.5 text-[16px] leading-[1.55] text-body">
                    Cut to size, counted and banded. What most hand-applied lines and most export orders ask for, because a sheet travels and stores without a core.
                </p>
            </div>

            <div class="bg-paper lg:pl-12">
                <div class="aspect-[16/10] bg-well">
                    @if ($media['roll'])
                        <img src="{{ $media['roll'] }}" alt="Battery labels wound on rolls" width="1200" height="750" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
                <h3 class="mt-7 text-[26px] leading-[1.1] font-extrabold tracking-[-.03em]">Rolls</h3>
                <p class="mt-3.5 text-[16px] leading-[1.55] text-body">
                    Wound to your applicator. Four numbers settle it, and the quote form asks for all four — get them wrong and the labels come off the roll upside down.
                </p>
                <dl class="mt-7 border-t border-rule">
                    @foreach ($rollSpecs as [$term, $value])
                        <div class="flex justify-between gap-5 border-b border-rule py-3 text-[14.5px]">
                            <dt class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">{{ $term }}</dt>
                            <dd class="text-right font-semibold">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- 6. Packaging --}}
    <section id="packaging" class="mt-20 lg:mt-[108px]" aria-labelledby="packaging-title">
        <div class="on-dark bg-ink-deep text-white">
            <div class="site-container grid items-center gap-12 py-20 lg:grid-cols-[1fr_1fr] lg:gap-20 lg:py-[100px]">
                <div>
                    <p class="chip mb-[26px]">{{ $num('packaging') }} / The box around them</p>
                    <h2 id="packaging-title" class="text-[clamp(2rem,1.2rem+3vw,3.5rem)] leading-[1] font-extrabold tracking-[-.04em] text-balance">
                        The label is half<br> the shipment.
                    </h2>
                    <p class="mt-6 max-w-[52ch] text-[17px] leading-[1.55] text-on-dark">
                        A battery is heavy, and the box has to hold it through a truck, a warehouse and a shop floor. We print the labels and build the corrugated around them on the same floor — mono cartons up to nine-ply boxes — so the brand on the label and the brand on the box are the same colour.
                    </p>
                    <dl class="mt-10 grid grid-cols-2 gap-px bg-[#2b2b2b]">
                        @foreach ([['To 9-ply', 'Corrugation capability'], ['One floor', 'Labels and boxes together'], ['Offset · digital · flexo', 'Print processes in-house'], [$dispatch, 'Dispatch after approval']] as [$term, $caption])
                            <div class="flex flex-col-reverse bg-ink-deep px-4 py-5 sm:px-5">
                                <dt class="mt-1.5 font-mono text-[10.5px] leading-snug text-muted">{{ $caption }}</dt>
                                <dd class="text-[19px] font-extrabold tracking-[-.03em] sm:text-[22px]">{{ $term }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                <div class="aspect-[4/3] bg-[#1c1a1b]">
                    @if ($media['packaging'])
                        <img src="{{ $media['packaging'] }}" alt="Corrugated boxes printed for battery shipments" width="1200" height="900" loading="lazy" class="size-full object-cover">
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- 7. Clientele --}}
    @if ($clients->isNotEmpty())
        <section id="clients" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="clients-title">
            <x-site.section-header :num="$num('clients')" kicker="Our major clientele" title="Who prints with us." id="clients-title">
                Battery brands that have been through the acid test and stayed. We ship to {{ $countries->count() }} export markets besides.
            </x-site.section-header>

            {{-- Borders per cell, not a gap-px background: with 10 names in a 4-wide
                 grid the trailing cells would otherwise show as grey filler. --}}
            <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($clients as $client)
                    <li class="flex h-[104px] items-center justify-center border-r border-b border-rule px-5 lg:h-[128px]">
                        @if ($client->show_logo && $client->logo)
                            <img src="{{ ImageStore::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy" class="max-h-11 w-auto opacity-75 grayscale">
                        @else
                            <span class="text-center text-[24px] font-bold tracking-[-.035em] text-logo lg:text-[28px]">{{ $client->name }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            @if ($countries->isNotEmpty())
                <p class="mt-8 font-mono text-[11.5px] leading-[1.7] tracking-[.08em] text-mono uppercase">{{ $countries->pluck('name')->join(' · ') }}</p>
            @endif
        </section>
    @endif

    {{-- 8. Battery case studies --}}
    @if ($cases->isNotEmpty())
        <section id="work" class="site-container pt-20 lg:pt-[108px]" aria-labelledby="work-title">
            <x-site.section-header :num="$num('work')" kicker="On the line" title="What it looked like." id="work-title">
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

    {{-- 9. Sample request. Same fields as /request-a-sample, and the same intake. --}}
    <section id="sample" class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="sample-title">
        <x-site.section-header :num="$num('sample')" kicker="Test it yourself" title="Don’t take our word." id="sample-title">
            We will send a sheet with its spec. Put it in acid, put it on heat, try to lift it. That is what it is for.
        </x-site.section-header>

        <div class="grid gap-12 pt-12 lg:grid-cols-[1fr_340px] lg:gap-20">
            <form method="POST" action="{{ route('enquiry.store', 'sample') }}" id="form" class="scroll-mt-28">
                @csrf
                <x-site.honeypot bag="sample" />
                <x-site.form-errors bag="sample" />

                @include('enquiry.steps.sample', ['intent' => 'sample'])

                <div class="mt-9 border-t border-rule pt-8">
                    @include('enquiry.steps.contact', ['intent' => 'sample'])
                </div>

                <div class="mt-9 border-t border-rule pt-7">
                    <button type="submit" data-track="sample_submit" class="btn btn-brand w-full px-8 py-[18px] text-[16px] sm:w-auto">Request the sample</button>
                    <p class="mt-4 text-[13.5px] leading-[1.5] text-mono">We use this to send the sample and answer you. No list, no reselling.</p>
                </div>
            </form>

            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="bg-paper-mid p-7">
                    <h2 class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">What arrives</h2>
                    <ol class="mt-5 flex flex-col gap-4">
                        @foreach (['A sheet of the battery paper, printed.', 'The spec it was printed to.', 'The acid and heat results above, in writing.'] as $i => $line)
                            <li class="flex gap-3.5 text-[14.5px] leading-[1.5]">
                                <span class="font-mono text-[11px] text-mono" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                                <span>{{ $line }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
                @if ($whatsapp)
                    <p class="mt-8"><a href="{{ $whatsapp }}" target="_blank" rel="noopener" data-track="whatsapp" class="link-underline">Or ask on WhatsApp</a></p>
                @endif
            </aside>
        </div>
    </section>

    <x-site.cta-band body="Size, quantity, sheet or roll, and your artwork. A named coordinator replies within one working day.">
        <x-slot:title>Know the job?<br> Get a price.</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="url(config('site.international_url'))" data-track="international">Export enquiry</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
