@props(['num', 'kicker', 'title', 'dark' => false, 'id' => null])
{{-- Numbered kicker + H2 on the left, intro (slot) on the right, over the 2px rule --}}
<header @class([
    'flex flex-col gap-6 pb-[26px] lg:flex-row lg:items-end lg:justify-between lg:gap-[60px]',
    'border-b-2 border-ink' => ! $dark,
    'border-b border-[#2e2e2e]' => $dark,
])>
    <div>
        <p class="kicker mb-[18px]">{{ $num }} / {{ $kicker }}</p>
        <h2 @if ($id) id="{{ $id }}" @endif class="h-section">{{ $title }}</h2>
    </div>
    @if ($slot->isNotEmpty())
        <div {{ $attributes->class(['max-w-[42ch] text-[17px] leading-[1.55] lg:shrink-0', 'text-body' => ! $dark, 'text-on-dark' => $dark]) }}>
            {{ $slot }}
        </div>
    @endif
</header>
