<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Support\LeadIntake;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Downloads, and the optional email gate in front of them. A gated file is
 * released through a short-lived signed URL, so the address bar cannot be
 * shared around the form and the file itself is never guessable from the page.
 */
class DownloadController extends Controller
{
    private const LINK_MINUTES = 15;

    public function index(): View
    {
        return view('resources.downloads', [
            'groups' => Download::published()->whereNotNull('file')->ordered()->get()
                ->groupBy(fn (Download $download) => Download::CATEGORIES[$download->category] ?? 'Other documents'),
        ]);
    }

    /** POST — the email gate. Files a `download` lead, then hands over a signed link. */
    public function request(Request $request, Download $download): RedirectResponse
    {
        abort_unless($download->is_published && $download->file, 404);

        $validator = Validator::make($request->all(), LeadIntake::rules('download'), LeadIntake::messages());

        if ($validator->fails()) {
            return redirect()->to(route('downloads.index').'#download-'.$download->id)
                ->withErrors($validator, 'download-'.$download->id)
                ->withInput();
        }

        LeadIntake::create('download', $validator->validated() + ['download' => $download->title], $request);

        return redirect()->to($this->signedUrl($download));
    }

    public function file(Request $request, Download $download): StreamedResponse
    {
        abort_unless($download->is_published && $download->file, 404);

        // An ungated file needs no signature; a gated one is only released
        // through the link the form hands back.
        abort_if($download->requires_email && ! $request->hasValidSignature(), 403);

        abort_unless(Storage::disk('public_uploads')->exists($download->file), 404);

        return Storage::disk('public_uploads')->download(
            $download->file,
            $download->title.'.'.pathinfo($download->file, PATHINFO_EXTENSION),
        );
    }

    private function signedUrl(Download $download): string
    {
        return URL::temporarySignedRoute('downloads.file', now()->addMinutes(self::LINK_MINUTES), $download);
    }
}
