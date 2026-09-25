{{-- Quote step 1: what the job is --}}
<div class="grid gap-7 sm:grid-cols-2">
    <x-site.field name="product_type" label="What do you need?" :bag="$intent" :options="$productTypes"
                  :required="true" placeholder="Choose a product…" />
    <x-site.field name="industry" label="What is it for?" :bag="$intent" :options="$industries"
                  placeholder="Choose a sector…" help="Helps us pick the right paper and adhesive." />
</div>

<fieldset class="mt-7">
    <legend class="site-label">Printing process <span class="site-optional">optional — we will advise</span></legend>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
        @foreach ($technologies->concat(['Not sure']) as $option)
            <label class="site-choice">
                <input type="radio" name="technology" value="{{ $option }}" @checked($prev('technology') === $option)>
                <span>{{ $option }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
