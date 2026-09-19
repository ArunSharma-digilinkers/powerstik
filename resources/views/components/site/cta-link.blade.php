@props(['href', 'solid' => false, 'external' => false])
{{-- One action row in the red CTA band: white fill for the primary action, outlined for the rest --}}
<a href="{{ $href }}" @if ($external) target="_blank" rel="noopener" @endif
   {{ $attributes->class([
       'flex items-center justify-between gap-4 text-[17px] transition-colors',
       'bg-white px-7 py-[22px] font-bold text-ink hover:bg-ink hover:text-white' => $solid,
       'border border-white/50 px-7 py-[21px] font-semibold hover:border-white hover:bg-white/12' => ! $solid,
   ]) }}>
    {{ $slot }} <span class="font-mono text-[13px]" aria-hidden="true">→</span>
</a>
