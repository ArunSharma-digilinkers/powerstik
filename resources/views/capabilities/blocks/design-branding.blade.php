{{-- What arrives, and in what order --}}
<section class="site-container pt-20 lg:pt-[108px]" aria-labelledby="studio-title">
    <x-site.section-header num="02" kicker="How a design job runs" title="Drawn, shown, then made." id="studio-title">
        The mockup exists so you can reject it cheaply. Changing a 3D render costs nothing; changing a plate costs a make-ready.
    </x-site.section-header>

    <ol class="grid border-t border-l border-rule sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Brief', 'What the product is, where it sells, what it has to survive. Bring a reference if you have one.'],
            ['Draw', 'Dieline first, artwork second. The shape decides what the design can do.'],
            ['Show', 'A 3D mockup of the actual pack, not a flat PDF. This is where things change.'],
            ['Release', 'Print-ready files, separated and trapped for the press the job will run on.'],
        ] as $i => [$step, $body])
            <li class="flex flex-col border-r border-b border-rule px-7 py-9 lg:px-8 lg:py-10">
                <span class="font-mono text-[11.5px] text-faint">{{ sprintf('%02d', $i + 1) }}</span>
                <h3 class="mt-5 text-[22px] leading-[1.1] font-bold tracking-[-.025em]">{{ $step }}</h3>
                <p class="mt-3.5 text-[15.5px] leading-[1.55] text-body">{{ $body }}</p>
            </li>
        @endforeach
    </ol>

    <div class="mt-12 border-l-[3px] border-brand-500 pl-7">
        <p class="max-w-[62ch] text-[19px] leading-[1.5] font-semibold tracking-[-.01em]">
            The studio is why the rest of it exists. Powerstik bought its first press because the design work needed one — not the other way round.
        </p>
        <p class="mt-5"><a href="{{ url('/about#story') }}" class="link-underline">Read how that happened</a></p>
    </div>
</section>
