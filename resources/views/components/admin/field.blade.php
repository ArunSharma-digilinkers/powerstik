@props(['name', 'label', 'type' => 'text', 'value' => null, 'help' => null])

<div>
    <label for="{{ $name }}" class="adm-label">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="3" {{ $attributes->class('adm-input') }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->class('adm-input') }}>
    @endif
    @if ($help)
        <p class="mt-1 text-xs text-zinc-500">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
