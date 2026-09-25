{{-- Ply, and what each one is for. General board knowledge, not a client claim —
     no bursting strengths or compression figures, which we cannot verify here. --}}
<section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="ply-title">
    <x-site.section-header num="02" kicker="Picking the board" title="Ply is a load decision." id="ply-title">
        More ply is not better, it is heavier and dearer. The right answer comes from the weight inside, the stack height and how far it travels.
    </x-site.section-header>

    <ul class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Mono carton', 'Single board, no flute', 'Retail packs, pharma cartons, anything that sells off a shelf rather than ships on a pallet.'],
            ['3-ply', 'One flute', 'Light contents and short journeys. The everyday outer.'],
            ['5-ply', 'Two flutes', 'The workhorse. Heavier contents, stacked, on a truck.'],
            ['7 & 9-ply', 'Three and four flutes', 'Heavy or fragile, long journeys, high stacks. Where a box failing costs more than the box.'],
        ] as [$name, $spec, $body])
            <li class="flex flex-col border-r border-b border-rule px-7 py-9 lg:px-8 lg:py-10">
                <h3 class="text-[22px] leading-[1.1] font-bold tracking-[-.025em]">{{ $name }}</h3>
                <p class="label-mark mt-4">{{ $spec }}</p>
                <p class="mt-4 text-[15.5px] leading-[1.55] text-body">{{ $body }}</p>
            </li>
        @endforeach
    </ul>

    <div class="mt-12 grid gap-10 border-t border-rule pt-10 lg:grid-cols-[1.1fr_.9fr] lg:gap-16">
        <div>
            <p class="max-w-[62ch] text-[19px] leading-[1.5] font-semibold tracking-[-.01em]">
                Print, slot and die-cut run in line, so the box is not printed in one place and cut in another with a week in between.
            </p>
            <p class="mt-5 text-[16.5px] leading-[1.55] text-body">
                It also means the printed outer and the label inside it come off the same floor, matched to the same approved proof. A carton that is half a shade off the product inside it is the most common thing we are asked to fix.
            </p>
        </div>
        <dl class="grid grid-cols-2 gap-px self-start bg-rule-mid">
            @foreach ([['Box styles', 'Drawn to the product'], ['Flute', 'Picked for the stack'], ['Inserts', 'Partitions and pads'], ['Print', 'Matched to the label']] as [$term, $caption])
                <div class="flex flex-col-reverse bg-paper px-5 py-6">
                    <dt class="mt-1.5 font-mono text-[10.5px] leading-snug text-mono">{{ $caption }}</dt>
                    <dd class="text-[19px] font-extrabold tracking-[-.03em]">{{ $term }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
