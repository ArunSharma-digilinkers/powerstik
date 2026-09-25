@php
    use App\Support\Site;

    $headline = match ($type) {
        'callback' => 'We will ring you.',
        'sample' => 'The sample is on its way.',
        'international' => 'The export desk has it.',
        'contact' => 'Message received.',
        default => 'Got it.',
    };

    $next = match ($type) {
        'sample' => [
            'We pull a sheet from stock, or run one on the next make-ready.',
            'It ships with the spec and our acid and heat results.',
            'Test it however you like. Then we talk quantities.',
        ],
        'international' => [
            'The export desk checks freight and documents for your port.',
            'You get a price on your Incoterm, with a lead time.',
            'First order ships with full export documentation.',
        ],
        'callback' => [
            'A coordinator calls you on the number you gave us.',
            'Tell them the job; the written quote follows the same day.',
        ],
        'contact' => [
            'A person reads it — there is no ticket queue.',
            'You get a reply within one working day.',
        ],
        default => [
            'A coordinator reads the spec and checks your artwork — free.',
            'You get a price, a paper and a dispatch date in writing.',
            'Nothing goes on press until you approve a proof.',
        ],
    };
@endphp

<x-layouts.site title="Thank you" description="We have your enquiry and a coordinator will come back within one working day.">
    <section class="on-dark bg-ink text-white" data-lead-event="{{ $type }}" aria-labelledby="thanks-title">
        <div class="site-container pt-16 pb-20 lg:pt-[104px] lg:pb-[108px]">
            <div class="grid gap-12 lg:grid-cols-[1.15fr_.85fr] lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ strtolower($label) }} received</p>
                    <h1 id="thanks-title" class="text-[clamp(2.5rem,1rem+5vw,5.25rem)] leading-[.94] font-extrabold tracking-[-.04em]">{{ $headline }}</h1>
                    <p class="mt-7 max-w-[46ch] text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">
                        A coordinator picks this up and comes back to you within one working day.
                        @if ($reference)
                            Quote <strong class="font-bold text-white">#{{ $reference }}</strong> on anything you send us next.
                        @endif
                    </p>
                    @if ($reference)
                        <dl class="mt-9 inline-flex items-center gap-4 border border-rule-dark px-6 py-4">
                            <dt class="font-mono text-[11px] tracking-[.1em] text-muted uppercase">Reference</dt>
                            <dd class="text-[26px] leading-none font-extrabold tracking-[-.03em] tabular-nums">#{{ $reference }}</dd>
                        </dl>
                    @endif
                </div>

                <div class="border-t border-rule-dark pt-8 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-14">
                    <h2 class="font-mono text-[11px] tracking-[.1em] text-brand-500 uppercase">What happens next</h2>
                    <ol class="mt-6 flex flex-col">
                        @foreach ($next as $i => $line)
                            <li class="flex gap-4 border-b border-rule-dark py-4 text-[15.5px] leading-[1.5] text-on-dark last:border-0">
                                <span class="font-mono text-[11px] text-muted" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                                <span>{{ $line }}</span>
                            </li>
                        @endforeach
                    </ol>

                    <div class="mt-9 flex flex-wrap gap-3">
                        @if ($whatsapp)
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener" data-track="whatsapp_thanks" class="btn btn-brand px-7 py-4 text-[15.5px]">Add anything on WhatsApp</a>
                        @endif
                        @if ($phone)
                            <a href="{{ Site::telUrl($phone) }}" data-track="call" class="btn btn-ghost-dark px-7 py-4 text-[15.5px]">{{ $phone }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- While you are here. Only pages that exist are linked. --}}
    <section class="site-container pt-16 pb-20 lg:pt-[88px] lg:pb-[104px]" aria-labelledby="meanwhile-title">
        <h2 id="meanwhile-title" class="border-b-2 border-ink pb-[26px] text-[clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.05] font-extrabold tracking-[-.03em]">While you wait.</h2>
        <ul class="grid gap-px bg-rule sm:grid-cols-3">
            @foreach ([
                ['How we got here', 'One plant in Haryana, the same two founders since 2002.', url('/about')],
                ['Quality & PDI', 'What gets checked, at which stage, before a truck loads.', url('/about#quality')],
                ['The machines', 'Offset, digital, flexo and corrugation — all on one floor.', url('/#machines')],
            ] as [$title, $body, $href])
                <li class="bg-paper">
                    <a href="{{ $href }}" class="group flex h-full flex-col gap-3 bg-paper px-7 py-9 transition-colors hover:bg-paper-mid lg:px-8 lg:py-10">
                        <h3 class="text-[21px] font-extrabold tracking-[-.025em]">
                            <span class="box-decoration-clone transition-colors group-hover:bg-brand-500">{{ $title }}</span>
                        </h3>
                        <p class="text-[15px] leading-[1.5] text-body">{{ $body }}</p>
                        <span class="mt-auto pt-4 font-mono text-[11.5px] tracking-[.1em] uppercase">Read <span aria-hidden="true">→</span></span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
</x-layouts.site>
