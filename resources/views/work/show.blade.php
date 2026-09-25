@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();

    // A client is only named where they have given permission (the show_logo flag)
    $client = $project->client?->show_logo ? $project->client : null;

    $gallery = collect($project->gallery ?? [])->filter(fn ($path) => is_string($path) && $path !== '');

    // Problem → What we did → Result, but only the parts that have been written
    $story = array_filter([
        'Problem' => $project->problem,
        'What we did' => $project->solution,
        'Result' => $project->result,
    ], 'filled');

    $shown = array_keys(array_filter([
        'story' => $project->is_case_study && $story !== [],
        'gallery' => $gallery->isNotEmpty(),
        'related' => $related->isNotEmpty(),
    ]));
    $num = fn (string $section) => sprintf('%02d', array_search($section, $shown) + 1);

    // Each tag links back to the portfolio filtered by it
    $tags = collect()
        ->concat($project->industries->map(fn ($t) => ['industry', $t->slug, $t->name]))
        ->concat($project->productTypes->map(fn ($t) => ['product', $t->slug, $t->name]))
        ->concat($project->technologies->map(fn ($t) => ['tech', $t->slug, $t->name]));
@endphp

<x-layouts.site :title="$project->seo_title ?: $project->title"
                :description="$project->seo_description ?: $project->summary">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="project-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Our work' => route('work.index'), $project->title => route('work.show', $project)]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    @if ($project->card_tag)
                        <p class="chip mb-[26px]">{{ $project->card_tag }}</p>
                    @elseif ($project->is_case_study)
                        <p class="chip mb-[26px]">Case study</p>
                    @endif
                    <h1 id="project-title" class="text-[clamp(2rem,1rem+3.8vw,4.25rem)] leading-[1] font-extrabold tracking-[-.04em] text-balance">{{ $project->title }}</h1>
                    @if ($client)
                        <p class="mt-7 font-mono text-[11.5px] tracking-[.12em] text-muted uppercase">For {{ $client->name }}</p>
                    @endif
                </div>
                @if ($project->summary)
                    <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">{{ $project->summary }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- 2. Cover --}}
    @if ($project->cover_image)
        <div class="bg-ink">
            <img src="{{ ImageStore::url($project->cover_image) }}" srcset="{{ ImageStore::srcset($project->cover_image) }}"
                 sizes="100vw" alt="{{ $project->title }}" class="aspect-[16/7] w-full object-cover">
        </div>
    @endif

    {{-- 3. The headline result, where there is one --}}
    @if ($project->result_headline)
        <section class="border-b border-rule bg-paper-mid" aria-label="Result">
            <div class="site-container flex flex-col gap-6 py-12 sm:flex-row sm:items-center sm:justify-between sm:gap-12 lg:py-[68px]">
                <div>
                    <p class="label-mark">What changed</p>
                    <p class="mt-4 text-[clamp(1.75rem,1.2rem+1.8vw,2.75rem)] leading-[1.05] font-extrabold tracking-[-.035em]">{{ $project->result_headline }}</p>
                </div>
                @if ($tags->isNotEmpty())
                    <ul class="flex flex-wrap gap-2 sm:justify-end">
                        @foreach ($tags as [$key, $slug, $name])
                            <li>
                                <a href="{{ route('work.index', [$key => $slug]) }}"
                                   class="block border border-rule-mid bg-paper px-4 py-2 font-mono text-[11px] tracking-[.08em] uppercase transition-colors hover:border-ink hover:bg-brand-500">{{ $name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    @endif

    {{-- 4. Problem → What we did → Result --}}
    @if ($project->is_case_study && $story !== [])
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="story-title">
            <x-site.section-header :num="$num('story')" kicker="How it went" title="What actually happened." id="story-title" />
            <div class="grid gap-12 pt-12 lg:grid-cols-3 lg:gap-14">
                @foreach ($story as $heading => $body)
                    <div class="border-t-2 border-ink pt-6">
                        <h3 class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">{{ $heading }}</h3>
                        <div class="prose-site mt-5">{!! $body !!}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 5. Gallery --}}
    @if ($gallery->isNotEmpty())
        <section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="gallery-title">
            <x-site.section-header :num="$num('gallery')" kicker="On the job" title="From the floor." id="gallery-title" />
            <ul class="grid gap-6 pt-10 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($gallery as $image)
                    <li class="aspect-[4/3] bg-well">
                        <img src="{{ ImageStore::url($image) }}" srcset="{{ ImageStore::srcset($image) }}"
                             sizes="(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw"
                             alt="{{ $project->title }}" loading="lazy" class="size-full object-cover">
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 6. Related --}}
    @if ($related->isNotEmpty())
        <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="related-title">
            <x-site.section-header :num="$num('related')" kicker="Nearby" title="Same sector, different problem." id="related-title">
                <a href="{{ route('work.index') }}" class="inline-block border-b-2 border-brand-500 pb-1 font-mono text-[12px] tracking-[.1em] whitespace-nowrap text-ink uppercase transition-colors hover:border-ink">See all work</a>
            </x-site.section-header>
            <ul class="grid gap-7 pt-10 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $other)
                    <li>
                        <a href="{{ route('work.show', $other) }}" class="group flex h-full flex-col border border-rule transition-colors hover:border-ink">
                            <div class="aspect-[3/2] overflow-hidden bg-well">
                                @if ($other->cover_image)
                                    <img src="{{ ImageStore::url($other->cover_image) }}" srcset="{{ ImageStore::srcset($other->cover_image) }}"
                                         sizes="(min-width: 1024px) 400px, 50vw" alt="{{ $other->title }}" loading="lazy"
                                         class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col px-[26px] pt-[26px] pb-[30px]">
                                @if ($other->card_tag)
                                    <p class="label-mark">{{ $other->card_tag }}</p>
                                @endif
                                <h3 class="mt-3.5 text-[22px] leading-[1.15] font-bold tracking-[-.025em]">
                                    <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $other->title }}</span>
                                </h3>
                                @if ($other->result_headline)
                                    <p class="mt-auto border-t border-rule pt-4 text-[14.5px] font-bold">{{ $other->result_headline }}</p>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <x-site.cta-band body="Tell us what is going wrong with the one you have now. That is usually the fastest way in.">
        <x-slot:title>Got a problem<br> like this?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
