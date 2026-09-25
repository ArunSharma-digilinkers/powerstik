{{-- International step 1: the market. The country list suggests where we already
     ship, but stays a free text field so a new market is not turned away. --}}
<div class="grid gap-7 sm:grid-cols-2">
    <div>
        <label for="country-{{ $intent }}" class="site-label">Destination country</label>
        <input id="country-{{ $intent }}" name="country" type="text" list="export-countries" required
               value="{{ $prev('country') }}" class="site-input" placeholder="e.g. Nigeria"
               @if ($bagErrors->has('country')) aria-invalid="true" aria-describedby="country-{{ $intent }}-error" @endif>
        <datalist id="export-countries">
            @foreach ($countries as $country)
                <option value="{{ $country }}"></option>
            @endforeach
        </datalist>
        @if ($bagErrors->has('country'))
            <p id="country-{{ $intent }}-error" class="site-error">{{ $bagErrors->first('country') }}</p>
        @endif
    </div>
    <x-site.field name="port" label="Port of discharge" :bag="$intent" placeholder="e.g. Apapa, Lagos" help="Or the nearest one you clear through." />
    <x-site.field name="product_type" label="What do you need?" :bag="$intent" :options="$productTypes" :required="true" placeholder="Choose a product…" />
    <x-site.field name="annual_volume" label="Volume a year" :bag="$intent" placeholder="e.g. 2 million labels" />
</div>

<fieldset class="mt-7">
    <legend class="site-label">Incoterm <span class="site-optional">optional</span></legend>
    <div class="grid grid-cols-3 gap-2 sm:grid-cols-5">
        @foreach (['EXW', 'FOB', 'CFR', 'CIF', 'Not sure'] as $option)
            <label class="site-choice justify-center">
                <input type="radio" name="incoterm" value="{{ $option }}" @checked($prev('incoterm') === $option)>
                <span>{{ $option }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
