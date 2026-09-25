@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();

    $labels = ['industry' => 'Sector', 'product' => 'Product', 'tech' => 'Process'];

    // A link to this page with one facet set or cleared, keeping the others
    $withFacet = fn (string $key, ?string $slug) => route('work.index', array_filter(
        array_merge($active, [$key => $slug]),
    ));
@endphp

<x-layouts.site title="Our work"
                description="Case studies and portfolio work from the Powerstik floor — problem, what we did, and what changed. Filter by sector, product or print process.">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="work-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Our work' => route('work.index')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $total }} {{ \Illuminate\Support\Str::plural('project', $total) }}</p>
                    <h1 id="work-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Problem, press,<br> result.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    Every one of these started as somebody's problem — a label that lifted, a box that collapsed, a carton that would not fit the mandated copy. The interesting part is what changed.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. Filters. Plain links, so every view has its own URL and none of it needs JavaScript. --}}
    @if (collect($facets)->contains(fn ($terms) => $terms->isNotEmpty()))
        <section class="border-b border-rule bg-paper-mid" aria-labelledby="filters-title">
            <div class="site-container py-8 lg:py-10">
                <h2 id="filters-title" class="sr-only">Filter the portfolio</h2>
                <div class="flex flex-col gap-5">
                    @foreach ($facets as $key => $terms)
                        @continue ($terms->isEmpty())
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-baseline sm:gap-6">
                            <p class="w-[72px] shrink-0 font-mono text-[11px] tracking-[.1em] text-mono uppercase">{{ $labels[$key] }}</p>
                            <ul class="flex flex-wrap gap-2">
                                <li>
                                    <a href="{{ $withFacet($key, null) }}" @if (! isset($active[$key])) aria-current="true" @endif
                                       class="block border px-4 py-2 text-[14.5px] font-semibold transition-colors border-rule-mid hover:border-ink aria-[current]:border-ink aria-[current]:bg-ink aria-[current]:text-white">All</a>
                                </li>
                                @foreach ($terms as $term)
                                    <li>
                                        <a href="{{ $withFacet($key, $term->slug) }}" @if (($active[$key] ?? null) === $term->slug) aria-current="true" @endif
                                           class="flex items-center gap-2 border px-4 py-2 text-[14.5px] font-semibold transition-colors border-rule-mid hover:border-ink hover:bg-brand-500 aria-[current]:border-ink aria-[current]:bg-ink aria-[current]:text-white">
                                            {{ $term->name }}
                                            <span class="font-mono text-[10.5px] text-mono aria-[current]:text-muted">{{ $term->projects_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                @if ($active)
                    <p class="mt-7 border-t border-rule-mid pt-5 text-[14.5px]">
                        Showing {{ $projects->total() }} of {{ $total }}.
                        <a href="{{ route('work.index') }}" class="link-underline ml-2 text-[14.5px]">Clear filters</a>
                    </p>
                @endif
            </div>
        </section>
    @endif

    {{-- 3. The work --}}
    <section class="site-container pt-16 pb-20 lg:pt-20 lg:pb-[104px]" aria-label="Portfolio">
        @if ($projects->isEmpty())
            <div class="border-l-[3px] border-brand-500 py-2 pl-7">
                <p class="text-[22px] leading-[1.3] font-bold tracking-[-.02em]">Nothing here yet under that combination.</p>
                <p class="mt-3 max-w-[54ch] text-[16.5px] leading-[1.55] text-body">
                    It does not mean we have not done it — most of what we print never becomes a case study.
                    <a href="{{ route('work.index') }}" class="link-underline">Clear the filters</a>, or
                    <a href="{{ route('contact') }}" class="link-underline">ask us directly</a>.
                </p>
            </div>
        @else
            <ul class="grid gap-7 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @foreach ($projects as $project)
                    <li>
                        <a href="{{ route('work.show', $project) }}" class="group flex h-full flex-col border border-rule transition-colors hover:border-ink">
                            <div class="aspect-[3/2] overflow-hidden bg-well">
                                @if ($project->cover_image)
                                    <img src="{{ ImageStore::url($project->cover_image) }}" srcset="{{ ImageStore::srcset($project->cover_image) }}"
                                         sizes="(min-width: 1024px) 400px, (min-width: 768px) 50vw, 100vw" alt="{{ $project->title }}"
                                         loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col px-[26px] pt-[26px] pb-[30px]">
                                @if ($project->card_tag)
                                    <p class="label-mark">{{ $project->card_tag }}</p>
                                @elseif ($project->industries->isNotEmpty())
                                    <p class="label-mark">{{ $project->industries->pluck('name')->join(' · ') }}</p>
                                @endif
                                <h2 class="mt-3.5 text-[23px] leading-[1.15] font-bold tracking-[-.025em]">
                                    <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $project->title }}</span>
                                </h2>
                                @if ($project->summary)
                                    <p class="mt-3 text-[15px] leading-[1.55] text-body">{{ $project->summary }}</p>
                                @endif
                                @if ($project->result_headline)
                                    <p class="mt-auto border-t border-rule pt-4 text-[14.5px] font-bold">{{ $project->result_headline }}</p>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-14"><x-site.pagination :paginator="$projects" /></div>
        @endif
    </section>

    <x-site.cta-band body="Most of what we print never becomes a case study. Send the spec and we will tell you what we have done that is close to it.">
        <x-slot:title>Your job<br> next?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
