@props(['paginator'])
{{-- Square, ink, no shadows — Laravel's bundled pagination views are rounded and
     shadowed, so the public site uses this instead. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-6 border-t-2 border-ink pt-6">
        @if ($paginator->onFirstPage())
            <span class="font-mono text-[11.5px] tracking-[.1em] text-faint uppercase" aria-hidden="true">← Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="font-mono text-[11.5px] tracking-[.1em] uppercase transition-colors hover:text-body">← Previous</a>
        @endif

        <p class="font-mono text-[11.5px] tracking-[.1em] text-mono uppercase">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </p>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="font-mono text-[11.5px] tracking-[.1em] uppercase transition-colors hover:text-body">Next →</a>
        @else
            <span class="font-mono text-[11.5px] tracking-[.1em] text-faint uppercase" aria-hidden="true">Next →</span>
        @endif
    </nav>
@endif
