@php
    use App\Support\Site;
    $whatsapp = Site::whatsappUrl();
    $next = match ($lead->type) {
        'quote' => ['A coordinator reads the spec and checks the artwork.', 'We come back with a price, a paper and a dispatch date.', 'You approve a proof before anything goes on press.'],
        'sample' => ['We pull a sample from stock or run one on the next make-ready.', 'It ships with the spec sheet and our test results.', 'Test it however you like — acid, heat, adhesion.'],
        'international' => ['We check freight and documentation for your port.', 'You get a quote on your Incoterm, with lead times.', 'First order ships with full export documentation.'],
        'callback' => ['A coordinator calls you on the number you gave us.', 'Tell them the job; they will send the quote in writing.'],
        default => ['A coordinator picks this up and replies in person.', 'If it is a job, the written quote follows.'],
    };
@endphp
<x-mail.layout preheader="We have your enquiry. Reference #{{ $lead->id }}.">
    <p style="margin:0 0 6px; font-family:'Courier New',monospace; font-size:12px; letter-spacing:.1em; text-transform:uppercase; color:#6a6768;">Reference #{{ $lead->id }}</p>
    <h1 style="margin:0 0 18px; font-size:28px; line-height:1.1; font-weight:800; letter-spacing:-.025em;">Thank you, {{ $lead->name }}.</h1>

    <p style="margin:0 0 22px;">We have your {{ strtolower(App\Models\Lead::TYPES[$lead->type] ?? 'enquiry') }} and a coordinator will come back to you within one working day. Quote this reference on anything you send us: <strong>#{{ $lead->id }}</strong>.</p>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
        @foreach ($next as $i => $step)
            <tr>
                <td width="34" style="padding:6px 0; font-family:'Courier New',monospace; font-size:12px; color:#6a6768; vertical-align:top;">{{ sprintf('%02d', $i + 1) }}</td>
                <td style="padding:6px 0;">{{ $step }}</td>
            </tr>
        @endforeach
    </table>

    @if ($whatsapp)
        <p style="margin:28px 0 0;">
            <a href="{{ $whatsapp }}" style="display:inline-block; background:#c7ff00; color:#221f20; font-weight:bold; text-decoration:none; padding:14px 26px;">Add anything on WhatsApp</a>
        </p>
    @endif

    <p style="margin:26px 0 0; color:#6a6768;">If this was not you, ignore this email and nothing happens.</p>
</x-mail.layout>
