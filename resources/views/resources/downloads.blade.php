@php
    use App\Support\ImageStore;
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $all = $groups->flatten();

    // Old input is page-wide, so only the gate that actually failed repopulates
    $failed = collect($errors->getBags())->keys()->first(fn (string $bag) => str_starts_with($bag, 'download-'));
@endphp

<x-layouts.site title="Downloads"
                description="The Powerstik company profile, catalogues and paper data sheets, as PDFs.">
    <section class="on-dark bg-ink text-white" aria-labelledby="downloads-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Resources' => route('resources.index'), 'Downloads' => route('downloads.index')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $all->count() }} {{ \Illuminate\Support\Str::plural('document', $all->count()) }}</p>
                    <h1 id="downloads-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Take it with<br> you.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    The company profile, what we can print, and the paper specifications behind it. Most of it opens straight away; where we ask for an email, the button says so first.
                </p>
            </div>
        </div>
    </section>

    <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-label="Downloads">
        @forelse ($groups as $category => $downloads)
            <div class="mb-14 last:mb-0">
                <h2 class="kicker mb-7">{{ $category }}</h2>
                <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($downloads as $download)
                        @php $bag = 'download-'.$download->id; @endphp
                        <li id="download-{{ $download->id }}" class="flex scroll-mt-28 flex-col border-r border-b border-rule px-7 py-8 lg:px-8 lg:py-9">
                            @if ($download->cover_image)
                                <div class="mb-6 aspect-[3/4] max-w-[140px] bg-well">
                                    <img src="{{ ImageStore::url($download->cover_image) }}" srcset="{{ ImageStore::srcset($download->cover_image) }}"
                                         sizes="140px" alt="{{ $download->title }}" loading="lazy" class="size-full object-cover">
                                </div>
                            @endif

                            <h3 class="text-[21px] leading-[1.15] font-bold tracking-[-.025em]">{{ $download->title }}</h3>
                            @if ($download->description)
                                <p class="mt-3 text-[15px] leading-[1.55] text-body">{{ $download->description }}</p>
                            @endif

                            @if ($download->requires_email)
                                <form method="POST" action="{{ route('downloads.request', $download) }}" class="mt-auto pt-6">
                                    @csrf
                                    <x-site.honeypot :bag="$bag" />
                                    <p class="label-mark mb-4">Email first, then the file</p>

                                    <div class="flex flex-col gap-4">
                                        <x-site.field name="name" label="Your name" :bag="$bag" :required="true" autocomplete="name"
                                                      :value="$failed === $bag ? old('name') : null" />
                                        <x-site.field name="email" label="Email" :bag="$bag" type="email" :required="true" autocomplete="email"
                                                      :value="$failed === $bag ? old('email') : null" />
                                        <x-site.field name="company" label="Company" :bag="$bag" autocomplete="organization"
                                                      :value="$failed === $bag ? old('company') : null" />
                                    </div>

                                    <button type="submit" data-track="download_gated" class="btn btn-ink mt-6 w-full px-6 py-3.5 text-[15px]">Send me the PDF</button>
                                </form>
                            @else
                                <p class="mt-auto pt-6">
                                    <a href="{{ route('downloads.file', $download) }}" data-track="download"
                                       class="btn btn-brand w-full px-6 py-3.5 text-[15px]">Download the PDF</a>
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            {{-- Nothing uploaded yet (Admin → Downloads). Home and the footer both link
                 here, so this has to be a useful page rather than an apology. --}}
            <div class="grid gap-12 lg:grid-cols-[1.1fr_.9fr] lg:gap-16">
                <div class="border-l-[3px] border-brand-500 py-2 pl-7">
                    <p class="text-[clamp(1.5rem,1.2rem+1vw,2rem)] leading-[1.2] font-extrabold tracking-[-.03em]">Nothing to download here yet.</p>
                    <p class="mt-5 max-w-[54ch] text-[16.5px] leading-[1.55] text-body">
                        The company profile and the paper data sheets are not on the site yet. Ask and a coordinator will email them to you the same working day — it is quicker than it sounds.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}" data-track="downloads_empty_contact" class="btn btn-brand px-7 py-4 text-[15.5px]">Ask for the profile</a>
                        @if ($whatsapp)
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener" data-track="whatsapp" class="btn border border-rule-mid px-7 py-4 text-[15.5px] font-semibold hover:border-ink">WhatsApp us</a>
                        @endif
                    </div>
                </div>
                <div class="bg-paper-mid p-8">
                    <p class="label-mark">Meanwhile</p>
                    <ul class="mt-5 flex flex-col gap-3 text-[16px]">
                        <li><a href="{{ route('capabilities.index') }}" class="link-underline">What we can print</a></li>
                        <li><a href="{{ route('capabilities.machines') }}" class="link-underline">What we print it on</a></li>
                        <li><a href="{{ route('resources.material-guide') }}" class="link-underline">The material guide</a></li>
                        <li><a href="{{ route('resources.faqs') }}" class="link-underline">FAQs</a></li>
                    </ul>
                </div>
            </div>
        @endforelse
    </section>

    <x-site.cta-band body="A quote is more use than a brochure anyway. Send the spec and get a real number back.">
        <x-slot:title>Rather have<br> a price?</x-slot:title>
        <x-site.cta-link :href="url(config('site.quote_url'))" :solid="true" data-track="quote_cta">Start the smart RFQ</x-site.cta-link>
        <x-site.cta-link :href="route('contact')" data-track="contact_cta">Ask a person</x-site.cta-link>
        @if ($whatsapp)
            <x-site.cta-link :href="$whatsapp" :external="true" data-track="whatsapp_cta">WhatsApp us</x-site.cta-link>
        @endif
    </x-site.cta-band>
</x-layouts.site>
