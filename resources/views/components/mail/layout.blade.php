{{-- Inline styles only: email clients drop <style> blocks and every class with them.
     Lime is a fill here too, so the header bar is lime with ink type on it. --}}
@props(['preheader' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Powerstik</title>
</head>
<body style="margin:0; padding:0; background:#f3f3f1;">
    @if ($preheader)
        <div style="display:none; max-height:0; overflow:hidden; opacity:0;">{{ $preheader }}</div>
    @endif
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f3f1;">
        <tr>
            <td align="center" style="padding:28px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%; max-width:600px; background:#ffffff;">
                    <tr>
                        <td style="background:#c7ff00; padding:18px 28px; font-family:'Courier New',monospace; font-size:12px; letter-spacing:.14em; text-transform:uppercase; color:#221f20;">
                            Powerstik{{ $eyebrow ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px; font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:1.6; color:#221f20;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#221f20; padding:22px 28px; font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:1.6; color:#8a8788;">
                            Powerstik — a brand of Design India<br>
                            {!! nl2br(e(App\Support\Site::setting('address'))) !!}
                            @if ($phone = App\Support\Site::setting('phone'))
                                <br><a href="{{ App\Support\Site::telUrl($phone) }}" style="color:#c7ff00; text-decoration:none;">{{ $phone }}</a>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
