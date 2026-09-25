@php
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $grouped = $terms->groupBy(fn ($term) => strtoupper($term->term[0] ?? ''));
@endphp

<x-layouts.site title="Material guide"
                description="Plain-English definitions of the words that turn up on a print quote — GSM, ply, flute, BOPP, lamination and the rest.">
    <section class="on-dark bg-ink text-white" aria-labelledby="guide-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Resources' => route('resources.index'), 'Material guide' => route('resources.material-guide')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $terms->count() }} {{ \Illuminate\Support\Str::plural('term', $terms->count()) }}</p>
                    <h1 id="guide-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        The words on<br> your quote.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Nobody should have to nod along to a supplier using words they have not explained. These are the ones that turn up most, in plain English.
                </p>
            </div>
        </div>
    </section>

    @if ($terms->isNotEmpty())
        {{-- A–Z jump bar. Plain anchors, so it works without JavaScript. --}}
        <nav class="sticky top-[72px] z-30 border-y border-rule bg-paper/94 backdrop-blur-[8px] min-[1100px]:top-[84px]" aria-label="Jump to letter">
            <ul class="site-container flex flex-wrap gap-1.5 py-4">
                @foreach ($letters as $letter)
                    <li>
                        <a href="#letter-{{ $letter }}"
                           class="grid size-9 place-items-center border border-rule-mid font-mono text-[12.5px] transition-colors hover:border-ink hover:bg-brand-500">{{ $letter }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif

    <section class="site-container pt-16 pb-20 lg:pt-20 lg:pb-[104px]" aria-label="Material guide">
        @forelse ($grouped as $letter => $group)
            <div id="letter-{{ $letter }}" class="mb-14 scroll-mt-[150px] last:mb-0">
                <h2 class="border-b-2 border-ink pb-4 text-[clamp(2rem,1.4rem+2vw,3rem)] leading-none font-extrabold tracking-[-.04em]">{{ $letter }}</h2>
                <dl>
                    @foreach ($group as $term)
                        <div id="term-{{ $term->slug }}" class="grid scroll-mt-[150px] gap-3 border-b border-rule py-7 lg:grid-cols-[280px_1fr] lg:gap-12">
                            <dt class="text-[21px] leading-[1.2] font-bold tracking-[-.025em]">{{ $term->term }}</dt>
                            <dd class="prose-site max-w-[68ch]">{!! $term->definition !!}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @empty
            <div class="border-l-[3px] border-brand-500 py-2 pl-7">
                <p class="text-[22px] leading-[1.3] font-bold tracking-[-.02em]">The guide is still being written.</p>
                <p class="mt-3 max-w-[54ch] text-[16.5px] leading-[1.55] text-body">
                    Ask us what a word on your quote means and we will explain it — <a href="{{ route('contact') }}" class="link-underline">write to us</a>.
                </p>
            </div>
        @endforelse
    </section>

    <x-site.cta-band body="Send us the spec in whatever words you have. Translating it is part of the job.">
        <x-slot:title>Still not<br> sure what to ask for?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('resources.faqs')" data-track="faqs">Read the FAQs</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
