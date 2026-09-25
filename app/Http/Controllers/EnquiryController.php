<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\Lead;
use App\Models\ProductType;
use App\Models\Technology;
use App\Support\LeadIntake;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Every conversion on the public site: the RFQ wizard and its two siblings
 * (sample, international), the contact page with its callback strip, and the
 * thank-you page. Validation and storage live in App\Support\LeadIntake.
 */
class EnquiryController extends Controller
{
    /**
     * The three wizard forms share one view. They differ in the steps they ask
     * for — each key is a partial in views/enquiry/steps — and the lead type
     * they file. `sample` and `international` exist as pages of their own until
     * /battery-labels#sample and /global#enquiry are built.
     */
    public const INTENTS = [
        'quote' => [
            'crumb' => 'Get a quote',
            'kicker' => 'Smart RFQ',
            'title' => 'Tell us the job.',
            'intro' => 'Four minutes of questions and you get a real number back — a price, a paper and a dispatch date. Not a brochure.',
            'steps' => ['job' => 'The job', 'spec' => 'Specification', 'contact' => 'You & the artwork'],
            'submit' => 'Send the RFQ',
        ],
        'sample' => [
            'crumb' => 'Request a sample',
            'kicker' => 'Battery label sample',
            'title' => 'Test it yourself.',
            'intro' => 'We will send a sheet of the battery paper with its spec. Put it in acid, put it on heat, try to lift it — that is what it is for.',
            'steps' => ['sample' => 'What to test', 'contact' => 'Where to send it'],
            'submit' => 'Request the sample',
        ],
        'international' => [
            'crumb' => 'International enquiry',
            'kicker' => 'Export desk',
            'title' => 'Shipping to your port.',
            'intro' => 'We already ship to {countries} markets. Tell us the country and the port, and we will quote on your Incoterm with the documentation included.',
            'steps' => ['market' => 'Your market', 'contact' => 'Your details'],
            'submit' => 'Send the enquiry',
        ],
    ];

    /** GET /get-a-quote · /request-a-sample · /international-enquiry */
    public function form(string $intent): View
    {
        $countries = Site::exportCountries();

        $meta = self::INTENTS[$intent];
        $meta['intro'] = str_replace('{countries}', (string) $countries->count(), $meta['intro']);

        return view('enquiry.form', [
            'intent' => $intent,
            'meta' => $meta,
            'productTypes' => ProductType::ordered()->pluck('name'),
            'industries' => Industry::published()->ordered()->pluck('name'),
            'technologies' => Technology::ordered()->pluck('name'),
            'countries' => $countries->pluck('name'),
        ]);
    }

    /** GET /contact */
    public function contact(): View
    {
        return view('enquiry.contact', [
            'address' => Site::setting('address'),
            'phone' => Site::setting('phone'),
            'phoneAlt' => Site::setting('phone_alt'),
            'email' => Site::setting('email'),
            'careersEmail' => Site::setting('careers_email'),
            'whatsapp' => Site::whatsappUrl(),
            'mapEmbed' => Site::setting('map_embed_url'),
        ]);
    }

    /** POST /enquiry/{type} — every form on the site posts here. */
    public function store(Request $request, string $type): RedirectResponse
    {
        $validator = Validator::make($request->all(), LeadIntake::rules($type), LeadIntake::messages());

        // Errors go back in a bag named after the type, so the two forms on the
        // contact page never show each other's messages.
        if ($validator->fails()) {
            return redirect()->to($this->formUrl($type))
                ->withErrors($validator, $type)
                ->withInput($request->except('artwork'));
        }

        $lead = LeadIntake::create($type, $validator->validated(), $request);

        return redirect()->route('thanks', $type)->with('lead', $lead->id);
    }

    /** GET /thank-you/{type} */
    public function thanks(Request $request, string $type): View
    {
        return view('enquiry.thanks', [
            'type' => $type,
            'label' => Lead::TYPES[$type] ?? 'enquiry',
            'reference' => $request->session()->get('lead'),
            'whatsapp' => Site::whatsappUrl(),
            'phone' => Site::setting('phone'),
        ]);
    }

    /** Where a failed submission bounces back to, anchored on the form itself. */
    private function formUrl(string $type): string
    {
        $anchor = match ($type) {
            'callback' => '#callback',
            'contact' => '#message',
            default => '#form',
        };

        $fallback = match ($type) {
            'contact', 'callback' => route('contact'),
            default => route($type),
        };

        // Only ever bounce back into this site, never to whatever sent the POST
        $previous = strtok((string) url()->previous(), '#');

        return (str_starts_with($previous, url('/')) ? $previous : $fallback).$anchor;
    }
}
