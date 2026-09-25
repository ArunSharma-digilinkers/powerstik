@php
    use App\Support\Site;

    $mapsQuery = $address ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(preg_replace('/\R/', ', ', $address)) : null;

    // Direct lines. Nothing here is invented: every destination is a setting or a
    // page that exists. Empty settings drop out rather than showing a placeholder.
    $lines = array_values(array_filter([
        $phone ? ['Call the plant', $phone, Site::telUrl($phone), 'call'] : null,
        $phoneAlt ? ['Second line', $phoneAlt, Site::telUrl($phoneAlt), 'call'] : null,
        $whatsapp ? ['WhatsApp', 'Click to chat', $whatsapp, 'whatsapp'] : null,
        $email ? ['Email', $email, 'mailto:'.$email, null] : null,
    ]));

    // A literal class, because Tailwind only ships the ones it can see in the source
    $lineColumns = match (count($lines)) {
        4 => 'lg:grid-cols-4',
        3 => 'lg:grid-cols-3',
        default => 'lg:grid-cols-2',
    };

    $desks = array_values(array_filter([
        ['Quotes & new jobs', 'Specs, quantities and a price in writing.', 'Start the RFQ', route('quote'), 'quote_contact'],
        ['Exports', 'Country, port and Incoterm — the export desk answers.', 'International enquiry', route('international'), 'international'],
        ['Samples', 'A sheet of the battery paper, with its test results.', 'Request a sample', route('sample'), 'sample'],
        ['Careers', 'Design studio, plant and QC roles.', $careersEmail ? 'Write to the studio' : 'See open roles', $careersEmail ? 'mailto:'.$careersEmail : url('/about#careers'), null],
    ]));
@endphp

<x-layouts.site title="Contact" description="Call the plant in Sonipat, message us on WhatsApp, or leave a note and a coordinator comes back within one working day.">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="contact-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Contact' => route('contact')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">Sonipat, Haryana</p>
                    <h1 id="contact-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">Talk to a person.</h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    One named coordinator per client, from the first enquiry to the reorder. Call the plant, send a note, or ask for a callback — all three reach the same desk.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. Direct lines --}}
    @if ($lines)
        <section class="border-t border-rule-dark bg-ink text-white" aria-label="Direct lines">
            <div class="site-container">
                <ul class="grid gap-px bg-rule-dark sm:grid-cols-2 {{ $lineColumns }}">
                    @foreach ($lines as [$label, $value, $href, $track])
                        <li class="bg-ink">
                            <a href="{{ $href }}" @if ($track === 'whatsapp') target="_blank" rel="noopener" @endif
                               @if ($track) data-track="{{ $track }}" @endif
                               class="group flex h-full flex-col justify-between gap-6 px-4 py-8 transition-colors hover:bg-ink-deep sm:px-6 lg:py-10">
                                <span class="font-mono text-[11px] tracking-[.1em] text-muted uppercase">{{ $label }}</span>
                                <span class="text-[19px] font-bold tracking-[-.02em] group-hover:text-brand-500 sm:text-[21px]">{{ $value }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- 3. The two forms --}}
    <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="write-title">
        <x-site.section-header num="01" kicker="Write to us" title="Leave it with us." id="write-title">
            Everything here lands in the same inbox and is answered by a person, normally within one working day.
        </x-site.section-header>

        <div class="grid gap-12 pt-12 lg:grid-cols-[1.25fr_.75fr] lg:gap-16">
            {{-- Message --}}
            <form method="POST" action="{{ route('enquiry.store', 'contact') }}" id="message" class="scroll-mt-28">
                @csrf
                <x-site.honeypot bag="contact" />
                <x-site.form-errors bag="contact" />

                <div class="grid gap-7 sm:grid-cols-2">
                    <x-site.field name="name" label="Your name" bag="contact" :required="true" autocomplete="name" />
                    <x-site.field name="company" label="Company" bag="contact" autocomplete="organization" />
                    <x-site.field name="email" label="Email" bag="contact" type="email" :required="true" autocomplete="email" />
                    <x-site.field name="phone" label="Phone" bag="contact" type="tel" autocomplete="tel" placeholder="+91 " />
                    <x-site.field class="sm:col-span-2" name="subject" label="What is it about" bag="contact"
                                  :options="['A new job', 'An order in progress', 'Artwork or a proof', 'Accounts', 'Something else']"
                                  placeholder="Choose one…" />
                    <x-site.field class="sm:col-span-2" name="message" label="Your message" bag="contact" type="textarea" :required="true" :rows="6" />
                </div>

                <div class="mt-9 border-t border-rule pt-7">
                    <button type="submit" data-track="contact_submit" class="btn btn-brand w-full px-8 py-[18px] text-[16px] sm:w-auto">Send the message</button>
                    <p class="mt-4 text-[13.5px] leading-[1.5] text-mono">We use this to answer you and nothing else. No list, no reselling.</p>
                </div>
            </form>

            {{-- Callback --}}
            <form method="POST" action="{{ route('enquiry.store', 'callback') }}" id="callback"
                  class="on-dark scroll-mt-28 self-start bg-ink px-7 py-9 text-white lg:sticky lg:top-28">
                @csrf
                <x-site.honeypot bag="callback" />
                <p class="chip">Rather be called?</p>
                <h3 class="mt-5 text-[26px] leading-[1.05] font-extrabold tracking-[-.03em]">We will ring you.</h3>
                <p class="mt-3 text-[15px] leading-[1.5] text-on-dark">Name and a number is all we need. No form to fill twice.</p>

                @if ($errors->getBag('callback')->any())
                    <div role="alert" class="mt-6 border-l-[3px] border-red-500 bg-red-500/10 px-4 py-3 text-[14px] text-red-200">
                        <ul class="space-y-1">
                            @foreach ($errors->getBag('callback')->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-7 flex flex-col gap-5 [&_.site-input]:border-[#454243] [&_.site-input]:bg-ink-deep [&_.site-input]:text-white [&_.site-input]:hover:border-white/60 [&_.site-input]:focus:border-brand-500 [&_.site-label]:text-muted">
                    <x-site.field name="name" label="Your name" bag="callback" :required="true" autocomplete="name" />
                    <x-site.field name="phone" label="Phone" bag="callback" type="tel" :required="true" autocomplete="tel" placeholder="+91 " />
                    <x-site.field name="preferred_time" label="Best time to call" bag="callback"
                                  :options="['Morning (9–12)', 'Afternoon (12–4)', 'Evening (4–7)', 'Any time']" placeholder="Any time" />
                </div>

                <button type="submit" data-track="callback_submit" class="btn btn-brand mt-8 w-full px-8 py-4 text-[15.5px]">Request the callback</button>
            </form>
        </div>
    </section>

    {{-- 4. Which desk --}}
    <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="desks-title">
        <x-site.section-header num="02" kicker="Straight to the right desk" title="Skip a step." id="desks-title">
            If you already know what you need, these go to the same people with the questions asked up front.
        </x-site.section-header>

        <ul class="grid gap-px border-b border-rule bg-rule sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($desks as [$title, $body, $action, $href, $track])
                <li class="bg-paper">
                    <a href="{{ $href }}" @if ($track) data-track="{{ $track }}" @endif
                       class="group flex h-full flex-col gap-3 bg-paper px-7 py-9 transition-colors hover:bg-paper-mid lg:px-8 lg:py-10">
                        <h3 class="text-[20px] font-extrabold tracking-[-.02em]">
                            <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $title }}</span>
                        </h3>
                        <p class="text-[15px] leading-[1.5] text-body">{{ $body }}</p>
                        <span class="mt-auto pt-4 font-mono text-[11.5px] tracking-[.1em] text-ink uppercase">{{ $action }} <span aria-hidden="true">→</span></span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- 5. Where we are --}}
    <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="where-title">
        <x-site.section-header num="03" kicker="Where we are" title="One plant, one address." id="where-title">
            Design, print, corrugation and dispatch all sit on the same floor in the Rai Industrial Estate.
        </x-site.section-header>

        <div class="grid gap-10 pt-12 lg:grid-cols-[.8fr_1.2fr] lg:gap-16">
            <div>
                <p class="label-mark">The plant</p>
                <address class="mt-5 text-[19px] leading-[1.5] font-semibold not-italic">
                    {!! nl2br(e($address)) !!}
                </address>
                @if ($mapsQuery)
                    <p class="mt-7"><a href="{{ $mapsQuery }}" target="_blank" rel="noopener" data-track="directions" class="link-underline">Get directions</a></p>
                @endif
                @if ($phone)
                    <p class="mt-3"><a href="{{ Site::telUrl($phone) }}" data-track="call" class="link-underline">{{ $phone }}</a></p>
                @endif
            </div>

            @if ($mapEmbed)
                <div class="aspect-[16/10] w-full bg-well">
                    <iframe src="{{ $mapEmbed }}" title="Powerstik on the map" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            class="h-full w-full border-0"></iframe>
                </div>
            @else
                {{-- No embed URL set yet (Settings → Contact). The address above still stands on its own. --}}
                <div class="flex aspect-[16/10] w-full items-center justify-center bg-well">
                    <p class="mono text-mono">Map</p>
                </div>
            @endif
        </div>
    </section>

    <x-site.cta-band body="Send the spec and get a price, a paper and a dispatch date in writing.">
        <x-slot:title>Know the job<br> already?</x-slot:title>
        <x-site.cta-link :href="route('quote')" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp">Message us on WhatsApp</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
