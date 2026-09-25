{{-- Quote step 2: the specification. The roll block only applies to roll jobs. --}}
<fieldset>
    <legend class="site-label">Sheets or rolls?</legend>
    <div class="grid grid-cols-3 gap-2">
        @foreach (['Sheets', 'Rolls', 'Not sure'] as $option)
            <label class="site-choice">
                <input type="radio" name="form" value="{{ $option }}" x-model="sheetOrRoll" @checked($prev('form') === $option)>
                <span>{{ $option }}</span>
            </label>
        @endforeach
    </div>
</fieldset>

<div class="mt-7 grid gap-7 sm:grid-cols-2">
    <x-site.field name="size" label="Size" :bag="$intent" placeholder="e.g. 90 × 45 mm" help="Flat size in mm, or the box's internal size." />
    <x-site.field name="quantity" label="Quantity" :bag="$intent" :required="true" placeholder="e.g. 50,000" help="A rough number is fine — we will quote slabs around it." />
    <x-site.field name="material" label="Material" :bag="$intent" placeholder="e.g. chromo sticker, 300 gsm board" help="Leave it blank and we will recommend one." />
    <x-site.field name="required_by" label="Needed by" :bag="$intent" type="date" />
</div>

<fieldset class="mt-7">
    <legend class="site-label">Finishing <span class="site-optional">optional — tick any</span></legend>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
        @foreach (['Gloss lamination', 'Matt lamination', 'Spot UV', 'Foil stamping', 'Embossing', 'Die-cut shape'] as $option)
            <label class="site-choice">
                <input type="checkbox" name="finish[]" value="{{ $option }}" @checked(in_array($option, (array) $prev('finish', []), true))>
                <span>{{ $option }}</span>
            </label>
        @endforeach
    </div>
</fieldset>

{{-- Roll specs. Alpine hides these for sheet jobs; without JS they stay visible
     and stay optional, because the server only requires them when form = Rolls. --}}
<div x-show="sheetOrRoll === 'Rolls'" class="mt-9 border-l-[3px] border-brand-500 pl-6">
    <p class="label-mark mb-5">Roll specification</p>
    <div class="grid gap-7 sm:grid-cols-2">
        <x-site.field name="core_size" label="Core size" :bag="$intent" conditional="sheetOrRoll === 'Rolls'"
                      :options="['1 inch', '1.5 inch', '3 inch']" placeholder="Choose a core…" />
        <x-site.field name="winding" label="Winding direction" :bag="$intent" conditional="sheetOrRoll === 'Rolls'"
                      :options="['Out 1', 'Out 2', 'Out 3', 'Out 4', 'In 1', 'In 2', 'In 3', 'In 4', 'Not sure']"
                      placeholder="Choose a direction…" help="Which way the label faces as it leaves the roll." />
        <x-site.field name="labels_per_roll" label="Labels per roll" :bag="$intent" placeholder="e.g. 1,000" />
        <x-site.field name="roll_od" label="Maximum roll diameter" :bag="$intent" placeholder="e.g. 250 mm" help="The largest roll your applicator takes." />
    </div>
</div>
