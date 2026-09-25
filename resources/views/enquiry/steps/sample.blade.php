{{-- Sample step 1: what the sample has to survive --}}
<x-site.field name="application" label="What will it go on?" :bag="$intent" :required="true"
              placeholder="e.g. 150Ah tubular battery, lead-acid"
              help="The tougher the surface, the more useful this is — acid, heat, oil, condensation." />

<div class="mt-7 grid gap-7 sm:grid-cols-2">
    <x-site.field name="label_type" label="Which label" :bag="$intent"
                  :options="['Battery labels', 'Sheet labels', 'Roll labels', 'Not sure yet']" placeholder="Choose one…" />
    <x-site.field name="sample_quantity" label="How many sheets" :bag="$intent" placeholder="e.g. 5" help="We normally send two or three." />
</div>

<x-site.field class="mt-7" name="message" label="Anything the sample has to prove" :bag="$intent" type="textarea" :rows="4"
              placeholder="Electrolyte strength, temperature, how long it has to hold…" />
