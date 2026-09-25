{{-- The four roll numbers, and where the RFQ asks for them --}}
<section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="rolls-title">
    <x-site.section-header num="02" kicker="Getting rolls right" title="Four numbers settle it." id="rolls-title">
        Get these wrong and the labels come off the roll upside down, or the reel will not fit the machine. The quote form asks for all four.
    </x-site.section-header>

    <div class="grid gap-12 pt-12 lg:grid-cols-[1.1fr_.9fr] lg:gap-16">
        <dl class="border-t border-rule">
            @foreach ([
                ['Core size', '1, 1.5 or 3 inch', 'Whatever your applicator mandrel takes.'],
                ['Winding direction', 'Out 1–4 or in 1–4', 'Which way the label faces as it leaves the reel.'],
                ['Labels per roll', 'Your call', 'A splice costs you a stop, so fewer, fuller rolls usually win.'],
                ['Maximum roll diameter', 'Tell us and we wind to it', 'The largest reel the machine will physically hold.'],
            ] as [$term, $value, $note])
                <div class="flex flex-col gap-1.5 border-b border-rule py-5 sm:flex-row sm:items-baseline sm:justify-between sm:gap-8">
                    <div>
                        <dt class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">{{ $term }}</dt>
                        <p class="mt-1.5 text-[15px] leading-[1.5] text-body">{{ $note }}</p>
                    </div>
                    <dd class="shrink-0 text-[17px] font-bold tracking-[-.02em] sm:text-right">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="bg-paper-mid p-8 lg:self-start">
            <p class="label-mark">Not sure of yours?</p>
            <p class="mt-5 text-[16.5px] leading-[1.55] text-body">
                Send a photograph of the reel currently on the machine, or the make and model of the applicator. We can usually read the rest off that.
            </p>
            <p class="mt-7"><a href="{{ url(config('site.quote_url')) }}?product_type=Roll+labels" data-track="quote_rolls" class="btn btn-ink px-7 py-4 text-[15.5px]">Start a roll label quote</a></p>
        </div>
    </div>

    <div class="mt-14 border-t border-rule pt-10">
        <p class="max-w-[62ch] text-[19px] leading-[1.5] font-semibold tracking-[-.01em]">
            Battery labels run on our own paper, developed in-house for electrolyte splash and bonnet heat. They have a page of their own.
        </p>
        <p class="mt-5"><a href="{{ route('battery-labels') }}" class="link-underline">Battery labels</a></p>
    </div>
</section>
