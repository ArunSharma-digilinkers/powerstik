{{-- Last step of every wizard: who you are, and the artwork if there is any --}}
<div class="grid gap-7 sm:grid-cols-2">
    <x-site.field name="name" label="Your name" :bag="$intent" :required="true" autocomplete="name" />
    <x-site.field name="company" label="Company" :bag="$intent" autocomplete="organization" />
    <x-site.field name="email" label="Email" :bag="$intent" type="email" :required="true" autocomplete="email"
                  :help="$intent === 'sample' ? 'We confirm the dispatch here.' : 'The quote goes here.'" />
    {{-- Don't suggest an Indian dialling code to someone enquiring from abroad --}}
    <x-site.field name="phone" label="Phone" :bag="$intent" type="tel" :required="true" autocomplete="tel"
                  :placeholder="$intent === 'international' ? 'With your country code' : '+91 '"
                  help="A call settles in two minutes what email takes a day to." />
    @if ($intent === 'sample')
        <x-site.field class="sm:col-span-2" name="address" label="Where to send the sample" :bag="$intent" type="textarea" :rows="3"
                      :required="true" autocomplete="street-address" placeholder="Street, city, state, PIN" />
    @else
        <x-site.field name="city" label="City" :bag="$intent" autocomplete="address-level2" />
        @if ($intent === 'quote')
            <x-site.field name="country" label="Country" :bag="$intent" :value="$prev('country', 'India')" autocomplete="country-name" />
        @endif
    @endif
</div>

@if ($intent !== 'sample')
    <x-site.field class="mt-7" name="message" label="Anything else we should know" :bag="$intent" type="textarea" :rows="4"
                  placeholder="Repeat job, a reference you like, a deadline we should design around…" />
@endif

@if ($intent === 'quote')
    <div class="mt-9 border-t border-rule pt-7">
        <label for="artwork" class="site-label">
            Artwork <span class="site-optional">optional — we check it free</span>
        </label>
        <input id="artwork" name="artwork[]" type="file" multiple
               accept=".{{ implode(',.', App\Support\LeadIntake::FILE_TYPES) }}"
               @change="files = [...$event.target.files].map((f) => f.name)"
               class="w-full border border-dashed border-rule-mid bg-paper-mid px-4 py-4 text-[14.5px] transition-colors file:mr-4 file:cursor-pointer file:border-0 file:bg-ink file:px-4 file:py-2.5 file:font-sans file:text-[13.5px] file:font-bold file:text-white hover:border-ink"
               @if ($bagErrors->has('artwork') || $bagErrors->hasAny(['artwork.0', 'artwork.1', 'artwork.2'])) aria-invalid="true" @endif>
        <p class="mt-2 text-[13.5px] leading-[1.5] text-mono">
            Up to {{ App\Support\LeadIntake::MAX_FILES }} files, {{ round(App\Support\LeadIntake::MAX_FILE_KB / 1024) }} MB each —
            {{ strtoupper(implode(', ', App\Support\LeadIntake::FILE_TYPES)) }}. Anything bigger, send us a link in the box above.
        </p>
        <ul x-cloak x-show="files.length" class="mt-3 flex flex-wrap gap-2">
            <template x-for="file in files" :key="file">
                <li class="chip" x-text="file"></li>
            </template>
        </ul>
        @foreach (['artwork', 'artwork.0', 'artwork.1', 'artwork.2'] as $key)
            @if ($bagErrors->has($key))
                <p class="site-error">{{ $bagErrors->first($key) }}</p>
            @endif
        @endforeach
    </div>
@endif
