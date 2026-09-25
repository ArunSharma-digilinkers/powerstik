@php
    use App\Support\LeadIntake;
    use App\Support\Site;

    $steps = array_keys($meta['steps']);
    $bagErrors = $errors->getBag($intent);

    // Which fields sit on which step, so a rejected submission reopens the first
    // step that has a message on it instead of a blank step 1.
    $fieldsByStep = [
        'job' => ['product_type', 'industry', 'technology'],
        'spec' => ['form', 'size', 'quantity', 'material', 'finish', 'required_by', 'core_size', 'winding', 'labels_per_roll', 'roll_od'],
        'sample' => ['application', 'label_type', 'sample_quantity'],
        'market' => ['country', 'port', 'product_type', 'annual_volume', 'incoterm'],
        'contact' => ['name', 'company', 'email', 'phone', 'city', 'address', 'message', 'artwork'],
    ];

    $openAt = 1;
    foreach ($steps as $i => $key) {
        if (collect($fieldsByStep[$key])->contains(fn ($field) => $bagErrors->has($field))) {
            $openAt = $i + 1;
            break;
        }
    }

    // Old input is shared page-wide, so only a form that actually failed repopulates
    $prev = fn (string $key, $default = null) => $bagErrors->any() ? old($key, $default) : $default;

    $phone = Site::setting('phone');
    $email = Site::setting('email');
    $whatsapp = Site::whatsappUrl();

    $next = match ($intent) {
        'sample' => [
            'We pull a sheet from stock, or run one on the next make-ready.',
            'It ships with the spec and our acid and heat results.',
            'Test it however you like. Then we talk quantities.',
        ],
        'international' => [
            'The export desk checks freight and documents for your port.',
            'You get a price on your Incoterm, with a lead time.',
            'First order ships with full export documentation.',
        ],
        default => [
            'A coordinator reads the spec and checks your artwork — free.',
            'You get a price, a paper and a dispatch date in writing.',
            'Nothing goes on press until you approve a proof.',
        ],
    };

    $facts = array_filter([
        ['Reply', 'Within 1 working day'],
        ['Dispatch', Site::setting('dispatch_days', '2–5 days').' after approval'],
        $intent === 'quote' ? ['Minimum', 'From '.Site::setting('moq', '50 units')] : null,
        ['Pricing', 'Quote-only, never published'],
    ]);
@endphp

<x-layouts.site :title="$meta['crumb']" :description="$meta['intro']">
    {{-- 1. Hero --}}
    <section class="on-dark bg-ink text-white" aria-labelledby="form-title">
        <div class="site-container pt-14 pb-16 lg:pt-[88px] lg:pb-[84px]">
            <x-site.breadcrumb :items="[$meta['crumb'] => url()->current()]" />
            <div class="grid gap-9 lg:grid-cols-[1.15fr_.85fr] lg:items-end lg:gap-20">
                <div>
                    <p class="chip mb-[26px]">{{ $meta['kicker'] }}</p>
                    <h1 id="form-title" class="text-[clamp(2.25rem,1rem+4.6vw,4.875rem)] leading-[.96] font-extrabold tracking-[-.04em]">{{ $meta['title'] }}</h1>
                </div>
                <p class="text-[17px] leading-[1.55] text-on-dark sm:text-[19px]">{{ $meta['intro'] }}</p>
            </div>
        </div>
    </section>

    {{-- 2. The form, with the direct lines beside it --}}
    <div class="site-container grid gap-14 pt-16 pb-20 lg:grid-cols-[1fr_340px] lg:gap-20 lg:pt-[76px] lg:pb-[104px]">
        <div>
            <form method="POST" action="{{ route('enquiry.store', $intent) }}" enctype="multipart/form-data"
                  id="form" class="scroll-mt-28"
                  x-data="{
                      step: {{ $openAt }},
                      count: {{ count($steps) }},
                      sheetOrRoll: @js(old('form', '')),
                      files: [],
                      go(to) {
                          if (to > this.step) {
                              const invalid = [...this.$refs['step' + this.step].querySelectorAll('input, select, textarea')]
                                  .find((el) => !el.checkValidity());
                              if (invalid) return invalid.reportValidity();
                          }
                          this.step = Math.min(this.count, Math.max(1, to));
                          this.$refs.top.scrollIntoView({ block: 'start' });
                      },
                  }">
                @csrf
                <x-site.honeypot :bag="$intent" />
                <div x-ref="top" class="scroll-mt-28"></div>
                <x-site.form-errors :bag="$intent" />

                {{-- Step meter. x-cloak keeps it hidden without JS, where every step is simply open. --}}
                <ol x-cloak class="mb-10 flex gap-px bg-rule" aria-hidden="true">
                    @foreach ($steps as $i => $key)
                        <li class="flex-1 bg-paper">
                            <button type="button" @click="go({{ $i + 1 }})"
                                    class="w-full border-t-[3px] pt-3 pb-1 text-left font-mono text-[11px] tracking-[.1em] uppercase transition-colors"
                                    :class="step >= {{ $i + 1 }} ? 'border-brand-500 text-ink' : 'border-rule-mid text-faint'">
                                <span class="block">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="mt-1 block truncate normal-case tracking-normal">{{ $meta['steps'][$key] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ol>

                @foreach ($steps as $i => $key)
                    <section x-show="step === {{ $i + 1 }}" x-ref="step{{ $i + 1 }}" class="mb-12 last:mb-0">
                        <h2 class="site-step-title">
                            <span class="font-mono text-[12px] font-normal tracking-[.1em] text-mono">{{ sprintf('%02d', $i + 1) }}</span>
                            {{ $meta['steps'][$key] }}
                        </h2>
                        <div class="pt-7">
                            @include('enquiry.steps.'.$key)
                        </div>
                    </section>
                @endforeach

                {{-- Step nav: JS only. Without it the whole form is one page and the submit below is the only button. --}}
                <div x-cloak class="mt-10 flex items-center justify-between gap-4 border-t border-rule pt-7">
                    <button type="button" x-show="step > 1" @click="go(step - 1)"
                            class="btn border border-rule-mid px-6 py-3.5 font-semibold hover:border-ink">Back</button>
                    <span x-show="step === 1" aria-hidden="true"></span>
                    <button type="button" x-show="step < count" @click="go(step + 1)"
                            class="btn btn-ink ml-auto px-8 py-4 text-[15.5px]">Next step</button>
                </div>

                <div x-show="step === count" class="mt-10 border-t border-rule pt-7">
                    <button type="submit" data-track="{{ $intent }}_submit" class="btn btn-brand w-full px-8 py-[18px] text-[16px] sm:w-auto">{{ $meta['submit'] }}</button>
                    <p class="mt-4 text-[13.5px] leading-[1.5] text-mono">
                        We use this to answer your enquiry and nothing else. No list, no reselling.
                    </p>
                </div>
            </form>
        </div>

        {{-- Aside: the direct lines, then what happens next --}}
        <aside class="lg:sticky lg:top-28 lg:self-start">
            <div class="border-t-2 border-ink pt-6">
                <h2 class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">Rather just talk?</h2>
                <ul class="mt-4 flex flex-col gap-2.5 text-[16px] font-semibold">
                    @if ($phone)
                        <li><a href="{{ Site::telUrl($phone) }}" data-track="call" class="link-underline">{{ $phone }}</a></li>
                    @endif
                    @if ($whatsapp)
                        <li><a href="{{ $whatsapp }}" target="_blank" rel="noopener" data-track="whatsapp" class="link-underline">WhatsApp us</a></li>
                    @endif
                    @if ($email)
                        <li><a href="mailto:{{ $email }}" class="link-underline break-all">{{ $email }}</a></li>
                    @endif
                    <li><a href="{{ route('contact') }}" class="link-underline">All contacts</a></li>
                </ul>
            </div>

            <div class="mt-10 bg-paper-mid p-7">
                <h2 class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">What happens next</h2>
                <ol class="mt-5 flex flex-col gap-4">
                    @foreach ($next as $i => $line)
                        <li class="flex gap-3.5 text-[14.5px] leading-[1.5]">
                            <span class="font-mono text-[11px] text-mono" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>

            <dl class="mt-10 border-t border-rule">
                @foreach ($facts as [$term, $definition])
                    <div class="flex justify-between gap-5 border-b border-rule py-3.5 text-[14px]">
                        <dt class="font-mono text-[11px] tracking-[.1em] text-mono uppercase">{{ $term }}</dt>
                        <dd class="text-right font-semibold">{{ $definition }}</dd>
                    </div>
                @endforeach
            </dl>
        </aside>
    </div>
</x-layouts.site>
