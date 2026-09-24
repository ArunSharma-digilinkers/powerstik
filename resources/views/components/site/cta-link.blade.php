@props(['href', 'solid' => false, 'external' => false])
{{-- One action row in the lime CTA band: ink fill for the primary action, outlined for the rest --}}
<a href="{{ $href }}" @if ($external) target="_blank" rel="noopener" @endif
   {{ $attributes->class([
       'flex items-center justify-between gap-4 text-[17px] transition-colors',
       'bg-ink px-7 py-[22px] font-bold text-white hover:bg-white hover:text-ink' => $solid,
       'border border-ink/35 px-7 py-[21px] font-semibold hover:border-ink hover:bg-ink hover:text-brand-500' => ! $solid,
   ]) }}>
    {{ $slot }} <span class="font-mono text-[13px]" aria-hidden="true">→</span>
</a>
