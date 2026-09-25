<?php

namespace App\Support;

use App\Mail\LeadAcknowledged;
use App\Mail\LeadReceived;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Every public form on the site goes through here: validation rules per lead
 * type, the Lead row, its artwork files and the two notification emails.
 *
 * Fields that match a column on `leads` are stored there; everything else the
 * form sends lands in `payload`, which the admin lead screen prints as a list.
 */
class LeadIntake
{
    public const MAX_FILES = 3;

    public const MAX_FILE_KB = 8192;

    public const FILE_TYPES = ['pdf', 'ai', 'eps', 'cdr', 'svg', 'zip', 'png', 'jpg', 'jpeg'];

    /** Columns on `leads`. Anything else in the submission becomes `payload`. */
    private const COLUMNS = ['name', 'email', 'phone', 'company', 'country', 'city', 'message'];

    /** Never stored: CSRF, the honeypot, and the uploads (they become LeadFiles). */
    private const IGNORED = ['_token', 'website', 'artwork'];

    /**
     * Rules per type. The shared contact block is merged in first and the
     * per-type entry overwrites it — that is how `contact` makes the phone
     * optional and `callback` makes the email optional.
     *
     * @return array<string, array<int, string>>
     */
    public static function rules(string $type): array
    {
        $shared = [
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:4000'],
            'website' => ['nullable', 'string', 'max:190'], // honeypot; see create()
        ];

        $artwork = [
            'artwork' => ['nullable', 'array', 'max:'.self::MAX_FILES],
            'artwork.*' => ['file', 'max:'.self::MAX_FILE_KB, 'mimes:'.implode(',', self::FILE_TYPES)],
        ];

        return array_merge($shared, match ($type) {
            'quote' => $artwork + [
                'product_type' => ['required', 'string', 'max:120'],
                'industry' => ['nullable', 'string', 'max:120'],
                'technology' => ['nullable', 'string', 'max:60'],
                'form' => ['nullable', 'string', 'max:40'],
                'size' => ['nullable', 'string', 'max:120'],
                'quantity' => ['required', 'string', 'max:60'],
                'material' => ['nullable', 'string', 'max:160'],
                'finish' => ['nullable', 'array', 'max:10'],
                'finish.*' => ['string', 'max:60'],
                'required_by' => ['nullable', 'date'],
                // Roll specs: only asked for, and only required, when the job is on rolls
                'core_size' => ['nullable', 'required_if:form,Rolls', 'string', 'max:60'],
                'winding' => ['nullable', 'required_if:form,Rolls', 'string', 'max:60'],
                'labels_per_roll' => ['nullable', 'string', 'max:60'],
                'roll_od' => ['nullable', 'string', 'max:60'],
            ],
            'sample' => [
                'application' => ['required', 'string', 'max:160'],
                'label_type' => ['nullable', 'string', 'max:120'],
                'sample_quantity' => ['nullable', 'string', 'max:60'],
                'address' => ['required', 'string', 'max:500'], // there is no sample without somewhere to send it
            ],
            'international' => [
                'country' => ['required', 'string', 'max:120'],
                'port' => ['nullable', 'string', 'max:120'],
                'product_type' => ['required', 'string', 'max:120'],
                'annual_volume' => ['nullable', 'string', 'max:60'],
                'incoterm' => ['nullable', 'string', 'max:40'],
            ],
            'contact' => [
                'phone' => ['nullable', 'string', 'max:40'],
                'subject' => ['nullable', 'string', 'max:120'],
                'message' => ['required', 'string', 'max:4000'],
            ],
            'callback' => [
                'email' => ['nullable', 'email:rfc', 'max:190'],
                'preferred_time' => ['nullable', 'string', 'max:60'],
            ],
            default => [],
        });
    }

    /** @return array<string, string> */
    public static function messages(): array
    {
        return [
            'phone.required' => 'A phone number lets a coordinator call you back the same day.',
            'core_size.required_if' => 'Roll jobs need a core size — 1", 1.5" or 3".',
            'winding.required_if' => 'Tell us the winding direction so the labels come off the roll the right way up.',
            'artwork.*.mimes' => 'Artwork must be a PDF, AI, EPS, CDR, SVG, ZIP, PNG or JPG file.',
            'artwork.*.max' => 'Each file has to stay under '.round(self::MAX_FILE_KB / 1024).' MB. Send a link in the message for anything larger.',
        ];
    }

    /**
     * @param  array<string, mixed>  $data  validated input
     */
    public static function create(string $type, array $data, Request $request): Lead
    {
        // The honeypot is a field no person sees. A filled one is a bot, so the
        // lead is filed as spam rather than dropped: the admin inbox hides spam
        // by default, and a real person who somehow tripped it is not lost.
        $spam = filled($data['website'] ?? null);

        $lead = Lead::create(Arr::only($data, self::COLUMNS) + [
            'type' => $type,
            'status' => $spam ? 'spam' : 'new',
            'payload' => static::payload($data) ?: null,
            'source_url' => Str::limit((string) $request->headers->get('referer'), 490, ''),
            'utm' => $request->session()->get('utm') ?: null,
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 490, ''),
        ]);

        static::attach($lead, Arr::wrap($request->file('artwork') ?? []));

        if (! $spam) {
            static::notify($lead);
        }

        return $lead;
    }

    /** Where the admin notification goes: the setting, then the enquiry inbox. */
    public static function recipients(): array
    {
        $configured = Site::setting('lead_recipients') ?: Site::setting('email');

        return array_values(array_filter(
            array_map('trim', explode(',', (string) $configured)),
            fn (string $address) => filter_var($address, FILTER_VALIDATE_EMAIL) !== false,
        ));
    }

    /**
     * Everything the form sent that is not a column, an ignored field or blank.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function payload(array $data): array
    {
        return array_filter(
            Arr::except($data, array_merge(self::COLUMNS, self::IGNORED)),
            fn ($value) => is_array($value) ? $value !== [] : filled($value),
        );
    }

    /** @param  array<int, UploadedFile>  $files */
    private static function attach(Lead $lead, array $files): void
    {
        foreach (array_slice($files, 0, self::MAX_FILES) as $file) {
            $lead->files()->create([
                'path' => $file->store('leads/'.$lead->id, 'local'),
                'original_name' => Str::limit($file->getClientOriginalName(), 190, ''),
                'size' => $file->getSize(),
            ]);
        }
    }

    private static function notify(Lead $lead): void
    {
        if ($to = static::recipients()) {
            Mail::to($to)->queue(new LeadReceived($lead));
        }

        if ($lead->email) {
            Mail::to($lead->email)->queue(new LeadAcknowledged($lead));
        }
    }
}
