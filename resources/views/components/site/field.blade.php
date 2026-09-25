@props([
    'name',
    'label',
    'bag' => 'default',
    'type' => 'text',
    'required' => false,
    'conditional' => null,   // an Alpine expression: required only while it is true
    'help' => null,
    'options' => null,   // select: a list of values, or [value => label]
    'placeholder' => null,
    'value' => null,
    'rows' => 5,
    'autocomplete' => null,
])
@php
    $bagErrors = $errors->getBag($bag);
    $id = $name.'-'.$bag;
    $invalid = $bagErrors->has($name);
    // Old input is shared across the page, so only repopulate the form that failed
    $current = $bagErrors->any() ? old($name, $value) : $value;
    $described = array_filter([$help ? $id.'-help' : null, $invalid ? $id.'-error' : null]);
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="site-label">
        {{ $label }}
        @if ($conditional)
            {{-- Without Alpine the label stays "optional" and the server has the last word --}}
            <span class="site-optional" x-show="!({{ $conditional }})">optional</span>
        @elseif (! $required)
            <span class="site-optional">optional</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" class="site-input"
                  @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                  @if ($required) required @endif
                  @if ($conditional) x-bind:required="{{ $conditional }}" @endif
                  @if ($described) aria-describedby="{{ implode(' ', $described) }}" @endif
                  @if ($invalid) aria-invalid="true" @endif>{{ $current }}</textarea>
    @elseif ($options !== null)
        <select id="{{ $id }}" name="{{ $name }}" class="site-input"
                @if ($required) required @endif
                @if ($conditional) x-bind:required="{{ $conditional }}" @endif
                @if ($described) aria-describedby="{{ implode(' ', $described) }}" @endif
                @if ($invalid) aria-invalid="true" @endif>
            <option value="">{{ $placeholder ?? 'Choose one…' }}</option>
            @foreach ($options as $key => $option)
                @php $optionValue = is_int($key) ? $option : $key; @endphp
                <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $current }}" class="site-input"
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
               @if ($required) required @endif
               @if ($conditional) x-bind:required="{{ $conditional }}" @endif
               @if ($described) aria-describedby="{{ implode(' ', $described) }}" @endif
               @if ($invalid) aria-invalid="true" @endif>
    @endif

    @if ($help)
        <p id="{{ $id }}-help" class="mt-2 text-[13.5px] leading-[1.5] text-mono">{{ $help }}</p>
    @endif
    @if ($invalid)
        <p id="{{ $id }}-error" class="site-error">{{ $bagErrors->first($name) }}</p>
    @endif
</div>
