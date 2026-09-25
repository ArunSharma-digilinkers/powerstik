@props(['bag' => 'default'])
{{-- Summary above a form that failed validation. Red, not the brand colour. --}}
@php $bagErrors = $errors->getBag($bag); @endphp
@if ($bagErrors->any())
    <div role="alert" tabindex="-1" class="mb-8 border-l-[3px] border-red-600 bg-red-50 px-5 py-4">
        <p class="font-mono text-[11px] tracking-[.1em] text-red-700 uppercase">{{ $bagErrors->count() }} {{ $bagErrors->count() === 1 ? 'field needs' : 'fields need' }} attention</p>
        <ul class="mt-2 space-y-1 text-[14.5px] text-red-700">
            @foreach ($bagErrors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
