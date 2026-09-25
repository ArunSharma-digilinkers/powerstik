{{-- When each press wins. General print economics, not a client claim. --}}
<section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="press-title">
    <x-site.section-header num="02" kicker="Which press, and why" title="When each one wins." id="press-title">
        Owning all three is only worth something if we tell you the truth about which to use. Here is the reasoning we would apply to your job.
    </x-site.section-header>

    <div class="grid border-t border-l border-rule lg:grid-cols-3">
        @foreach ([
            ['Offset', 'Long runs · lowest unit cost', [
                'Best when' => 'The quantity is high enough to spread the plates',
                'Colour' => 'Process and spot, read against the proof through the run',
                'Watch for' => 'Plates and make-ready make a short run expensive',
            ]],
            ['Digital', 'Short runs · no plate', [
                'Best when' => 'The quantity is small, or every piece is different',
                'Colour' => 'Consistent piece to piece, no plate to wear',
                'Watch for' => 'Unit cost stays flat, so a long run costs more than offset',
            ]],
            ['Flexo', 'Roll labels · inline die-cut', [
                'Best when' => 'The job is a roll label that has to come off finished',
                'Colour' => 'Strong solids, built for self-adhesive stock',
                'Watch for' => 'Fine detail and tight registration favour offset',
            ]],
        ] as [$press, $spec, $rows])
            <div class="flex flex-col border-r border-b border-rule px-7 py-9 lg:px-8 lg:py-10">
                <h3 class="text-[28px] leading-[1] font-extrabold tracking-[-.035em]">{{ $press }}</h3>
                <p class="label-mark mt-4">{{ $spec }}</p>
                <dl class="mt-7 border-t border-rule">
                    @foreach ($rows as $term => $value)
                        <div class="border-b border-rule py-3.5 last:border-0">
                            <dt class="font-mono text-[10.5px] tracking-[.1em] text-mono uppercase">{{ $term }}</dt>
                            <dd class="mt-1.5 text-[15.5px] leading-[1.5]">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endforeach
    </div>

    <div class="mt-12 border-l-[3px] border-brand-500 pl-7">
        <p class="max-w-[62ch] text-[19px] leading-[1.5] font-semibold tracking-[-.01em]">
            A supplier with one press will always tell you your job suits that press. Ask for the arithmetic, not the recommendation.
        </p>
        <p class="mt-5"><a href="{{ route('capabilities.machines') }}" class="link-underline">See what is on the floor</a></p>
    </div>
</section>
