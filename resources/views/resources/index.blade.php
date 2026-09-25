@php
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();

    // Only offer what has something behind it; the rest would be a dead end.
    $cards = array_values(array_filter([
        ['Downloads', 'Company profile, catalogues and data sheets as PDFs.', route('downloads.index'), $counts['downloads'], 'document'],
        ['Material guide', 'Plain-English definitions of the words that turn up on a quote — GSM, ply, flute, BOPP.', route('resources.material-guide'), $counts['materials'], 'term'],
        ['FAQs', 'Minimums, lead times, artwork formats, export. The questions we answer most.', route('resources.faqs'), $counts['faqs'], 'question'],
        ['Insights', 'Notes from the floor on print, packaging and what goes wrong.', route('insights.index'), $counts['insights'], 'post'],
    ], fn (array $card) => $card[3] > 0));
@endphp

<x-layouts.site title="Resources"
                description="Downloads, a plain-English material guide, FAQs and notes from the floor — the reference material behind a Powerstik quote.">
    <section class="on-dark bg-ink text-white" aria-labelledby="resources-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Resources' => route('resources.index')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">Reference</p>
                    <h1 id="resources-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        The bits you<br> can look up.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Buying print involves a vocabulary nobody teaches you. Everything here exists so you can check a word, a number or a lead time without having to ask — though asking is also fine.
                </p>
            </div>
        </div>
    </section>

    <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="index-title">
        <x-site.section-header num="01" kicker="Reference" title="Look it up." id="index-title">
            Nothing here is gated except the odd document that asks for an email first, and we will tell you which before you click.
        </x-site.section-header>

        @if ($cards)
            <ul class="grid border-t border-l border-rule sm:grid-cols-2">
                @foreach ($cards as [$title, $body, $href, $count, $noun])
                    <li>
                        <a href="{{ $href }}" class="group flex h-full flex-col border-r border-b border-rule px-7 py-9 transition-colors hover:bg-paper-mid lg:px-9 lg:py-11">
                            <p class="label-mark">{{ $count }} {{ \Illuminate\Support\Str::plural($noun, $count) }}</p>
                            <h2 class="mt-5 text-[27px] leading-[1.08] font-extrabold tracking-[-.03em]">
                                <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $title }}</span>
                            </h2>
                            <p class="mt-4 max-w-[46ch] text-[16px] leading-[1.55] text-body">{{ $body }}</p>
                            <span class="mt-auto pt-8 font-mono text-[11.5px] tracking-[.1em] uppercase">Open <span aria-hidden="true">→</span></span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="border-l-[3px] border-brand-500 py-2 pl-7">
                <p class="text-[22px] leading-[1.3] font-bold tracking-[-.02em]">This shelf is still being stocked.</p>
                <p class="mt-3 max-w-[54ch] text-[16.5px] leading-[1.55] text-body">
                    Ask us for whatever you were looking for and we will send it — <a href="{{ route('contact') }}" class="link-underline">write to us</a>.
                </p>
            </div>
        @endif
    </section>

    <x-site.cta-band body="If the answer is not here, it is a two-minute phone call. A coordinator picks up.">
        <x-slot:title>Still not<br> sure?</x-slot:title>
        <x-site.cta-link :href="route('contact')" :solid="true" data-track="contact_cta">Ask a person</x-site.cta-link>
        <x-site.cta-link :href="url(config('site.quote_url'))" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
