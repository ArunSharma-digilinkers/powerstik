@php
    use App\Models\Lead;
    use Illuminate\Support\Str;
    $label = Lead::TYPES[$lead->type] ?? 'Enquiry';
    $rows = array_filter([
        'Company' => $lead->company,
        'Email' => $lead->email,
        'Phone' => $lead->phone,
        'City' => $lead->city,
        'Country' => $lead->country,
    ]);
@endphp
<x-mail.layout :preheader="$label.' from '.$lead->name">
    <x-slot:eyebrow> · new lead #{{ $lead->id }}</x-slot:eyebrow>

    <p style="margin:0 0 6px; font-family:'Courier New',monospace; font-size:12px; letter-spacing:.1em; text-transform:uppercase; color:#6a6768;">{{ $label }}</p>
    <h1 style="margin:0 0 20px; font-size:26px; line-height:1.15; font-weight:800; letter-spacing:-.02em;">{{ $lead->name }}</h1>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="font-size:15px;">
        @foreach ($rows as $key => $value)
            <tr>
                <td style="padding:7px 12px 7px 0; color:#6a6768; white-space:nowrap; vertical-align:top;">{{ $key }}</td>
                <td style="padding:7px 0; font-weight:bold;">{{ $value }}</td>
            </tr>
        @endforeach
        @foreach ($lead->payload ?? [] as $key => $value)
            <tr>
                <td style="padding:7px 12px 7px 0; color:#6a6768; white-space:nowrap; vertical-align:top;">{{ Str::headline($key) }}</td>
                <td style="padding:7px 0; font-weight:bold;">{{ is_array($value) ? implode(', ', $value) : $value }}</td>
            </tr>
        @endforeach
    </table>

    @if ($lead->message)
        <div style="margin:22px 0 0; border-left:3px solid #c7ff00; padding:2px 0 2px 16px; white-space:pre-line;">{{ $lead->message }}</div>
    @endif

    @if ($lead->files->isNotEmpty())
        <p style="margin:22px 0 0; color:#6a6768;">{{ $lead->files->count() }} artwork {{ Str::plural('file', $lead->files->count()) }} attached to the lead — open it in the admin to download.</p>
    @endif

    <p style="margin:28px 0 0;">
        <a href="{{ route('admin.leads.show', $lead) }}" style="display:inline-block; background:#221f20; color:#ffffff; font-weight:bold; text-decoration:none; padding:14px 26px;">Open lead #{{ $lead->id }}</a>
    </p>

    @if ($lead->source_url)
        <p style="margin:22px 0 0; font-size:12px; color:#8a8788;">Submitted from {{ $lead->source_url }}</p>
    @endif
</x-mail.layout>
