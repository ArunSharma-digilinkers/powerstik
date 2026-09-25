@php
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $quote = url(config('site.quote_url'));
@endphp

<x-layouts.site :title="$meta['nav']" :description="$meta['intro']">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="capability-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Capabilities' => route('capabilities.index'), $meta['nav'] => route('capabilities.show', $slug)]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $meta['kicker'] }}</p>
                    <h1 id="capability-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em] text-balance">{{ $meta['title'] }}</h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">{{ $meta['intro'] }}</p>
            </div>
        </div>
    </section>

    {{-- 2. Fact strip --}}
    <section class="border-t border-rule-dark bg-ink text-white" aria-label="{{ $meta['nav'] }} at a glance">
        <div class="site-container">
            <dl class="grid grid-cols-2 gap-px bg-rule-dark lg:grid-cols-4">
                @foreach ($meta['facts'] as [$term, $caption])
                    <div class="flex flex-col-reverse bg-ink px-4 py-8 sm:px-6 lg:pt-10 lg:pb-[42px]">
                        <dt class="mt-2.5 font-mono text-[11px] leading-snug tracking-[.1em] text-muted uppercase sm:text-[11.5px]">{{ $caption }}</dt>
                        <dd class="text-[clamp(1.5rem,1.1rem+1.1vw,2.125rem)] leading-[1.05] font-extrabold tracking-[-.035em]">{{ $term }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 3. What you get --}}
    <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="deliverables-title">
        <x-site.section-header num="01" kicker="What you get" title="On the delivery note." id="deliverables-title">
            No line item here is an extra. It is what the job includes when you ask us for {{ strtolower($meta['nav']) }}.
        </x-site.section-header>
        <ul class="grid border-t border-l border-rule sm:grid-cols-2">
            @foreach ($meta['deliverables'] as $item)
                <li class="flex gap-4 border-r border-b border-rule px-6 py-6 text-[16.5px] leading-[1.5] lg:px-8 lg:py-7">
                    <span class="mt-[11px] h-[3px] w-[18px] shrink-0 bg-brand-500" aria-hidden="true"></span>
                    <span>{{ $item }}</span>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- 4. The part that is only true of this discipline --}}
    @include('capabilities.blocks.'.$slug)

    {{-- 5. The other three --}}
    <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="others-title">
        <x-site.section-header num="03" kicker="The rest of the roof" title="They are never separate jobs." id="others-title">
            Artwork, plates, print and box never change hands between vendors. That is the whole argument for using one supplier.
        </x-site.section-header>
        <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($others as $otherSlug => $other)
                <li>
                    <a href="{{ route('capabilities.show', $otherSlug) }}" class="group flex h-full flex-col gap-3 border-r border-b border-rule px-7 py-9 transition-colors hover:bg-paper-mid lg:px-8 lg:py-10">
                        <h3 class="text-[21px] font-extrabold tracking-[-.025em]">
                            <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $other['nav'] }}</span>
                        </h3>
                        <p class="text-[15px] leading-[1.5] text-body">{{ $other['title'] }}</p>
                        <span class="mt-auto pt-4 font-mono text-[11.5px] tracking-[.1em] uppercase">Read <span aria-hidden="true">→</span></span>
                    </a>
                </li>
            @endforeach
            <li>
                <a href="{{ route('capabilities.machines') }}" class="group flex h-full flex-col gap-3 border-r border-b border-rule px-7 py-9 transition-colors hover:bg-paper-mid lg:px-8 lg:py-10">
                    <h3 class="text-[21px] font-extrabold tracking-[-.025em]">
                        <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">Machine park</span>
                    </h3>
                    <p class="text-[15px] leading-[1.5] text-body">What is actually on the floor.</p>
                    <span class="mt-auto pt-4 font-mono text-[11.5px] tracking-[.1em] uppercase">Read <span aria-hidden="true">→</span></span>
                </a>
            </li>
        </ul>
    </section>

    <x-site.cta-band body="Send the size, the quantity and what it has to survive. A coordinator replies with a price, a paper and a dispatch date within one working day.">
        <x-slot:title>Have a job<br> for us?</x-slot:title>
        <x-site.cta-link :href="$quote" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
