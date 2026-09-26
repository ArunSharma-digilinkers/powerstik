@php
    use App\Models\Machine;
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $labelsPerDay = Site::setting('labels_per_day', '5,00,000');
    $people = preg_replace('/\D/', '', Site::setting('people', '200+'));
    $total = $machines->flatten()->count();

    // Ordered by Machine::CATEGORIES, and only the categories that have something in them
    $groups = collect(Machine::CATEGORIES)
        ->map(fn (string $label, string $key) => [$label, $machines->get($key)])
        ->filter(fn (array $group) => $group[1]?->isNotEmpty() ?? false)
        ->values();
@endphp

<x-layouts.site title="Machine park"
                description="Offset, digital, flexo and corrugation on one floor in Haryana — what is actually on the floor, press by press.">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="park-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Capabilities' => route('capabilities.index'), 'Machine park' => route('capabilities.machines')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">What is on the floor</p>
                    <h1 id="park-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Every press,<br> named.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    A supplier who will not tell you what they print on is usually printing on somebody else's machine. Here is ours, press by press, with the specification beside it.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. Fact strip --}}
    <section class="border-t border-rule-dark bg-ink text-white" aria-label="The floor in numbers">
        <div class="site-container">
            <dl class="grid grid-cols-2 gap-px bg-rule-dark lg:grid-cols-4">
                @foreach ([
                    [$total, 'Presses and lines'],
                    [$groups->count(), 'Processes in-house'],
                    [$labelsPerDay, 'Labels a day'],
                    ['3 shifts', 'Production running'],
                ] as [$term, $caption])
                    <div class="flex flex-col-reverse bg-ink px-4 py-8 sm:px-6 lg:pt-10 lg:pb-[42px]">
                        <dt class="mt-2.5 font-mono text-[11px] leading-snug tracking-[.1em] text-muted uppercase sm:text-[11.5px]">{{ $caption }}</dt>
                        <dd class="text-[clamp(1.75rem,1.3rem+1.2vw,2.5rem)] leading-none font-extrabold tracking-[-.04em] tabular-nums">{{ $term }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 3. The presses, by process --}}
    @forelse ($groups as $i => [$label, $group])
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="group-{{ $i }}">
            <x-site.section-header :num="sprintf('%02d', $i + 1)" kicker="{{ $label }}" title="{{ $label }}." id="group-{{ $i }}">
                {{ $group->count() }} {{ \Illuminate\Support\Str::plural('line', $group->count()) }} on the floor.
            </x-site.section-header>

            <ul class="grid border-t border-l border-rule sm:grid-cols-2">
                @foreach ($group as $machine)
                    <li class="flex flex-col border-r border-b border-rule px-7 py-9 lg:px-9 lg:py-10">
                        {{-- Only a photograph uploaded against this machine in the admin.
                             The maker's cutouts in public/media/machines are not mapped in
                             here: one of them is a different press from the one the brochure
                             credits, and a single picture cannot stand for two Speedmasters.
                             See docs/design/brand.md. --}}
                        @if ($machine->image)
                            {{-- aspect-ratio and overflow-hidden, like the industry cards. A
                                 fixed-height grid well does not constrain the photograph: the
                                 implicit row is auto, so max-h-full has nothing definite to
                                 resolve against and the image spills over the card. --}}
                            <div class="mb-7 aspect-[4/3] overflow-hidden bg-well">
                                <img src="{{ ImageStore::url($machine->image) }}" srcset="{{ ImageStore::srcset($machine->image) }}"
                                     sizes="(min-width: 640px) 50vw, 100vw"
                                     alt="The {{ $machine->name }} on the floor at Powerstik" loading="lazy"
                                     class="size-full object-cover">
                            </div>
                        @endif

                        @if ($machine->make || $machine->model)
                            <p class="label-mark">{{ collect([$machine->make, $machine->model])->filter()->join(' · ') }}</p>
                        @endif

                        <h3 class="mt-4 text-[24px] leading-[1.1] font-extrabold tracking-[-.03em]">{{ $machine->name }}</h3>

                        @if ($machine->description)
                            <p class="mt-3.5 text-[15.5px] leading-[1.55] text-body">{{ $machine->description }}</p>
                        @endif

                        @if (filled($machine->specs))
                            <dl class="mt-7 border-t border-rule">
                                @foreach ($machine->specs as $term => $value)
                                    <div class="flex justify-between gap-5 border-b border-rule py-3 text-[14.5px] last:border-0">
                                        <dt class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">{{ $term }}</dt>
                                        <dd class="text-right font-semibold">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @empty
        <section class="site-container py-20 lg:py-[108px]">
            <p class="text-[19px] text-body">The machine list is being updated. <a href="{{ route('contact') }}" class="link-underline">Ask us what a job would run on</a>.</p>
        </section>
    @endforelse

    {{-- 4. Why it matters --}}
    <section class="mt-20 lg:mt-[108px]" aria-labelledby="why-title">
        <div class="on-dark bg-ink-deep text-white">
            <div class="site-container grid gap-12 py-20 lg:grid-cols-[1fr_1fr] lg:gap-20 lg:py-[100px]">
                <div>
                    <p class="chip mb-[26px]">Why the list is public</p>
                    <h2 id="why-title" class="text-[clamp(2rem,1.2rem+3vw,3.5rem)] leading-[1] font-extrabold tracking-[-.04em] text-balance">
                        Nothing goes out<br> of sight.
                    </h2>
                    <p class="mt-6 max-w-[52ch] text-[17px] leading-[1.55] text-on-dark">
                        When a job is subcontracted you lose two things: the schedule, because you are now in someone else's queue, and the colour, because the reorder runs on a machine nobody kept a record of. Owning the presses is how the {{ Site::setting('dispatch_days', '2–5 days') }} and the colour match are the same promise.
                    </p>
                    <p class="mt-8"><a href="{{ url('/about#quality') }}" class="link-underline">How each run is checked</a></p>
                </div>
                <dl class="grid grid-cols-2 gap-px self-start bg-[#2e2e2e]">
                    @foreach ([
                        ['One queue', 'Yours, not a subcontractor’s'],
                        ['One record', 'The reorder runs where the original did'],
                        ['One coordinator', 'Named, and reachable'],
                        [$people.'+', 'People running the floor'],
                    ] as [$term, $caption])
                        <div class="flex flex-col-reverse bg-ink-deep px-5 py-7">
                            <dt class="mt-1.5 font-mono text-[10.5px] leading-snug text-muted">{{ $caption }}</dt>
                            <dd class="text-[20px] font-extrabold tracking-[-.03em] sm:text-[22px]">{{ $term }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- 5. The disciplines --}}
    <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="back-title">
        <x-site.section-header :num="sprintf('%02d', $groups->count() + 1)" kicker="What we run on them" title="Four disciplines." id="back-title" />
        <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($others as $slug => $discipline)
                <li>
                    <a href="{{ route('capabilities.show', $slug) }}" class="group flex h-full flex-col gap-3 border-r border-b border-rule px-7 py-9 transition-colors hover:bg-paper-mid lg:px-8 lg:py-10">
                        <h3 class="text-[21px] font-extrabold tracking-[-.025em]">
                            <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $discipline['nav'] }}</span>
                        </h3>
                        <p class="text-[15px] leading-[1.5] text-body">{{ $discipline['title'] }}</p>
                        <span class="mt-auto pt-4 font-mono text-[11.5px] tracking-[.1em] uppercase">Read <span aria-hidden="true">→</span></span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <x-site.cta-band body="Tell us the run and we will tell you which press it belongs on, and why.">
        <x-slot:title>Which press<br> is yours?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
