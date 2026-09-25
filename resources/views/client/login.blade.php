@php
    use App\Support\Site;

    $whatsapp = Site::whatsappUrl();
    $email = Site::setting('email');
@endphp

{{-- PLAN.md §3: the client zone is phase 2 and ships as "only a placeholder login
     link at launch". The utility bar and the footer both link here, so the
     placeholder is a real page rather than a 404. --}}
<x-layouts.site title="Client zone"
                description="The Powerstik client zone — order status, artwork approvals and invoices — is being built. Until it opens, your coordinator has everything.">
    <section class="on-dark bg-ink text-white" aria-labelledby="client-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="['Client zone' => url('/client/login')]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">Coming</p>
                    <h1 id="client-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">
                        Not open<br> yet.
                    </h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                    We are building a place to check an order, approve artwork and pull an invoice without picking up the phone. It is not ready, and we would rather say so than show you a login that does nothing.
                </p>
            </div>
        </div>
    </section>

    <section class="site-container pt-20 pb-20 lg:pt-[108px] lg:pb-[104px]" aria-labelledby="meanwhile-title">
        <x-site.section-header num="01" kicker="In the meantime" title="Your coordinator has all of it." id="meanwhile-title">
            Everything the client zone will show you, one named person can already tell you — usually faster than a login would.
        </x-site.section-header>

        <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Order status', 'Where your job is on the floor, and when it dispatches.'],
                ['Artwork & proofs', 'The approved version, and what changed since the last one.'],
                ['Reorders', 'Matched to the retained sample, not to a fresh file.'],
                ['Invoices', 'Sent on dispatch, resent whenever accounts asks.'],
            ] as [$title, $body])
                <li class="flex flex-col border-r border-b border-rule px-7 py-9 lg:px-8 lg:py-10">
                    <h3 class="text-[21px] leading-[1.1] font-bold tracking-[-.025em]">{{ $title }}</h3>
                    <p class="mt-3.5 text-[15.5px] leading-[1.55] text-body">{{ $body }}</p>
                </li>
            @endforeach
        </ul>

        <div class="mt-12 flex flex-wrap gap-3">
            <a href="{{ route('contact') }}" data-track="client_zone_contact" class="btn btn-brand px-7 py-4 text-[15.5px]">Ask your coordinator</a>
            @if ($email)
                <a href="mailto:{{ $email }}" class="btn border border-rule-mid px-7 py-4 text-[15.5px] font-semibold hover:border-ink">{{ $email }}</a>
            @endif
            @if ($whatsapp)
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener" data-track="whatsapp" class="btn border border-rule-mid px-7 py-4 text-[15.5px] font-semibold hover:border-ink">WhatsApp us</a>
            @endif
        </div>
    </section>
</x-layouts.site>
