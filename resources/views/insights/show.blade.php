@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->title,
        'datePublished' => $post->published_at->toIso8601String(),
        'dateModified' => $post->updated_at?->toIso8601String(),
        'publisher' => ['@type' => 'Organization', 'name' => 'Powerstik'],
    ] + array_filter([
        'description' => $post->excerpt,
        'image' => $post->cover_image ? ImageStore::url($post->cover_image) : null,
        'author' => $post->author ? ['@type' => 'Person', 'name' => $post->author->name] : null,
    ]);
@endphp

<x-layouts.site :title="$post->seo_title ?: $post->title" :description="$post->seo_description ?: $post->excerpt">
    <x-slot:head>
        <script type="application/ld+json">@json($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    </x-slot:head>

    <section class="on-dark bg-ink text-white" aria-labelledby="post-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Resources' => route('resources.index'), 'Insights' => route('insights.index'), $post->title => route('insights.show', $post)]" />
            <div class="max-w-[24ch]">
                <p class="chip mb-[26px]">{{ $post->category?->name ?? 'Insight' }}</p>
                <h1 id="post-title" class="text-[clamp(2rem,1rem+3.8vw,4.25rem)] leading-[1] font-extrabold tracking-[-.04em] text-balance">{{ $post->title }}</h1>
            </div>
            <p class="mt-8 flex flex-wrap items-center gap-x-3 font-mono text-[11.5px] tracking-[.1em] text-muted uppercase">
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('j F Y') }}</time>
                @if ($post->author)
                    <span aria-hidden="true">·</span> {{ $post->author->name }}
                @endif
            </p>
        </div>
    </section>

    @if ($post->cover_image)
        <div class="bg-ink">
            <img src="{{ ImageStore::url($post->cover_image) }}" srcset="{{ ImageStore::srcset($post->cover_image) }}"
                 sizes="100vw" alt="{{ $post->title }}" class="aspect-[16/7] w-full object-cover">
        </div>
    @endif

    <article class="site-container pt-16 pb-20 lg:pt-20 lg:pb-[104px]">
        @if ($post->excerpt)
            <p class="max-w-[62ch] border-l-[3px] border-brand-500 pl-7 text-[19px] leading-[1.5] font-semibold tracking-[-.01em]">{{ $post->excerpt }}</p>
        @endif
        @if (filled($post->body))
            <div class="prose-site mt-12 max-w-[68ch]">{!! $post->body !!}</div>
        @endif
    </article>

    @if ($related->isNotEmpty())
        <section class="site-container pb-20 lg:pb-[104px]" aria-labelledby="related-title">
            <x-site.section-header num="01" kicker="Next" title="More from the floor." id="related-title">
                <a href="{{ route('insights.index') }}" class="inline-block border-b-2 border-brand-500 pb-1 font-mono text-[12px] tracking-[.1em] whitespace-nowrap text-ink uppercase transition-colors hover:border-ink">All insights</a>
            </x-site.section-header>
            <ul class="grid gap-8 pt-10 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $other)
                    <li>
                        <a href="{{ route('insights.show', $other) }}" class="group flex h-full flex-col border-t border-ink pt-4">
                            <time datetime="{{ $other->published_at->toDateString() }}" class="font-mono text-[11px] tracking-[.08em] text-mono uppercase">{{ $other->published_at->format('j M Y') }}</time>
                            <h3 class="mt-3 text-[21px] leading-[1.15] font-bold tracking-[-.025em]">
                                <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $other->title }}</span>
                            </h3>
                            @if ($other->excerpt)
                                <p class="mt-3 text-[15px] leading-[1.55] text-body">{{ $other->excerpt }}</p>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <x-site.cta-band body="Everything here came out of a real job. Tell us about yours.">
        <x-slot:title>Print<br> something?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
