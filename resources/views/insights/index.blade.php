@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
@endphp

<x-layouts.site title="Insights"
                description="Notes from the Powerstik floor on print, packaging, materials and what goes wrong.">
    <section class="on-dark bg-ink text-white" aria-labelledby="insights-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Resources' => route('resources.index'), 'Insights' => route('insights.index')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $total }} {{ \Illuminate\Support\Str::plural('post', $total) }}</p>
                    <h1 id="insights-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Notes from<br> the floor.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Written by the people who run the presses, mostly about things that went wrong once and never again.
                </p>
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="border-b border-rule bg-paper-mid" aria-label="Filter by category">
            <ul class="site-container flex flex-wrap gap-2 py-6">
                <li>
                    <a href="{{ route('insights.index') }}" @if (! $active) aria-current="true" @endif
                       class="block border px-4 py-2 text-[14.5px] font-semibold transition-colors border-rule-mid hover:border-ink aria-[current]:border-ink aria-[current]:bg-ink aria-[current]:text-white">All</a>
                </li>
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('insights.index', ['category' => $category->slug]) }}" @if ($active === $category->slug) aria-current="true" @endif
                           class="flex items-center gap-2 border px-4 py-2 text-[14.5px] font-semibold transition-colors border-rule-mid hover:border-ink hover:bg-brand-500 aria-[current]:border-ink aria-[current]:bg-ink aria-[current]:text-white">
                            {{ $category->name }}
                            <span class="font-mono text-[10.5px] text-mono">{{ $category->posts_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="site-container pt-16 pb-20 lg:pt-20 lg:pb-[104px]" aria-label="Insights">
        @if ($posts->isEmpty())
            <div class="border-l-[3px] border-brand-500 py-2 pl-7">
                <p class="text-[22px] leading-[1.3] font-bold tracking-[-.02em]">Nothing published here yet.</p>
                <p class="mt-3 max-w-[54ch] text-[16.5px] leading-[1.55] text-body">
                    In the meantime the <a href="{{ route('resources.material-guide') }}" class="link-underline">material guide</a> and the
                    <a href="{{ route('resources.faqs') }}" class="link-underline">FAQs</a> cover most of what people ask.
                </p>
            </div>
        @else
            <ul class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <li>
                        <a href="{{ route('insights.show', $post) }}" class="group flex h-full flex-col">
                            <div class="aspect-[3/2] overflow-hidden bg-well">
                                @if ($post->cover_image)
                                    <img src="{{ ImageStore::url($post->cover_image) }}" srcset="{{ ImageStore::srcset($post->cover_image) }}"
                                         sizes="(min-width: 1024px) 400px, (min-width: 768px) 50vw, 100vw" alt="{{ $post->title }}"
                                         loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
                                @endif
                            </div>
                            <div class="mt-4 flex flex-1 flex-col border-t border-ink pt-4">
                                <p class="flex flex-wrap items-center gap-x-3 font-mono text-[11px] tracking-[.08em] text-mono uppercase">
                                    <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('j M Y') }}</time>
                                    @if ($post->category)
                                        <span aria-hidden="true">·</span> {{ $post->category->name }}
                                    @endif
                                </p>
                                <h2 class="mt-3 text-[22px] leading-[1.15] font-bold tracking-[-.025em]">
                                    <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $post->title }}</span>
                                </h2>
                                @if ($post->excerpt)
                                    <p class="mt-3 text-[15px] leading-[1.55] text-body">{{ $post->excerpt }}</p>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-14"><x-site.pagination :paginator="$posts" /></div>
        @endif
    </section>

    <x-site.cta-band body="Reading about it is slower than asking. Send the spec and get a real answer back.">
        <x-slot:title>Got a job<br> in mind?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
