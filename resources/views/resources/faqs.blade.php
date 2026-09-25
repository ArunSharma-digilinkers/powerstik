@php
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $all = $groups->flatten();

    // FAQPage structured data, from the same answers the page prints
    $schema = $all->isEmpty() ? null : [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $all->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq->question,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags($faq->answer))],
        ])->values()->all(),
    ];
@endphp

<x-layouts.site title="FAQs" description="Minimum order quantities, lead times, artwork formats and export — the questions we are asked most, answered plainly.">
    @if ($schema)
        <x-slot:head>
            <script type="application/ld+json">@json($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
        </x-slot:head>
    @endif

    <section class="on-dark bg-ink text-white" aria-labelledby="faqs-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Resources' => route('resources.index'), 'FAQs' => route('resources.faqs')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $all->count() }} {{ \Illuminate\Support\Str::plural('question', $all->count()) }}</p>
                    <h1 id="faqs-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Asked often<br> enough.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Straight answers, including the ones that are not in our favour. If yours is not here, ask — the answer usually ends up on this page afterwards.
                </p>
            </div>
        </div>
    </section>

    <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-label="Frequently asked questions">
        @forelse ($groups as $category => $faqs)
            <div class="mb-14 last:mb-0">
                <h2 class="kicker mb-7">{{ $category }}</h2>
                <ul class="border-t-2 border-ink">
                    @foreach ($faqs as $faq)
                        <li class="border-b border-rule">
                            {{-- <details> rather than Alpine: it opens without JavaScript and is
                                 findable with the browser's own in-page search. --}}
                            <details class="group">
                                <summary class="flex cursor-pointer list-none items-start justify-between gap-6 py-6 text-[18.5px] leading-[1.35] font-bold tracking-[-.02em] transition-colors hover:text-body sm:text-[20px]">
                                    {{ $faq->question }}
                                    <span class="mt-1.5 grid size-6 shrink-0 place-items-center border border-rule-mid font-mono text-[15px] leading-none transition-colors group-open:border-ink group-open:bg-brand-500" aria-hidden="true">
                                        <span class="group-open:hidden">+</span><span class="hidden group-open:inline">–</span>
                                    </span>
                                </summary>
                                <div class="prose-site max-w-[68ch] pb-7">{!! $faq->answer !!}</div>
                            </details>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="border-l-[3px] border-brand-500 py-2 pl-7">
                <p class="text-[22px] leading-[1.3] font-bold tracking-[-.02em]">No questions up here yet.</p>
                <p class="mt-3 max-w-[54ch] text-[16.5px] leading-[1.55] text-body">
                    Be the first to ask one — <a href="{{ route('contact') }}" class="link-underline">write to us</a>.
                </p>
            </div>
        @endforelse
    </section>

    <x-site.cta-band body="Ask it and we will answer it. If it is a good question, it ends up on this page.">
        <x-slot:title>Question<br> not here?</x-slot:title>
        <x-site.cta-link :href="route('contact')" :solid="true" data-track="contact_cta">Ask a person</x-site.cta-link>
        <x-site.cta-link :href="url(config('site.quote_url'))" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
