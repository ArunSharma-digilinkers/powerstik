<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\ExportMap;
use App\Support\Site;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Fixed home page media. Drop optimised files at these paths under public/
     * and they appear; until then the design's dark or tinted wells show.
     */
    private const MEDIA = [
        'hero_video' => 'media/home/hero.mp4',
        'hero_poster' => 'media/home/hero.webp',
        'battery' => 'media/home/battery.webp',
        'acid_test' => 'media/home/acid-test.webp',
        'export_map' => 'media/home/export-map.webp',
        'founders' => 'media/home/founders.webp',
    ];

    public function __invoke(): View
    {
        $countries = Site::exportCountries();

        return view('home', [
            'counters' => $this->counters(),
            'clients' => Client::ordered()->get(),
            'industries' => Industry::published()->ordered()->limit(9)->get(),
            'countries' => $countries,
            'arcs' => ExportMap::arcs($countries->pluck('name')->all()),
            'cases' => Project::published()->where('is_featured', true)->ordered()->limit(3)->get(),
            'testimonials' => Testimonial::published()->ordered()->limit(3)->get(),
            'founders' => TeamMember::published()->where('is_leadership', true)->ordered()->limit(2)->get(),
            'foundersQuote' => Site::setting('founders_quote'),
            'media' => array_map(fn ($path) => is_file(public_path($path)) ? asset($path) : null, self::MEDIA),
        ]);
    }

    /**
     * Proof strip. Each value animates its first number from `from` up to the
     * number itself; `static` values render as they are.
     *
     * @return list<array{value: string, label: string, from?: int, static?: bool}>
     */
    private function counters(): array
    {
        $firstNumber = fn (?string $value) => (int) preg_replace('/\D/', '', (string) preg_replace('/^\D*([\d,]+).*$/', '$1', (string) $value));

        return [
            ['value' => (string) Site::setting('since_year', '2002'), 'label' => 'Designing since', 'from' => 1900],
            ['value' => (string) Site::setting('clients', '1,500+'), 'label' => 'Domestic clients', 'from' => 0],
            ['value' => (string) Site::setting('export_countries', '8'), 'label' => 'Export countries', 'from' => 0],
            ['value' => (string) Site::setting('people', '200+'), 'label' => 'People on the floor', 'from' => 0],
            ['value' => trim(preg_replace('/\s*days?$/i', '', (string) Site::setting('dispatch_days', '2–5'))), 'label' => 'Day dispatch', 'static' => true],
            ['value' => 'From '.$firstNumber(Site::setting('moq', '50')), 'label' => 'Unit minimum', 'from' => 1],
        ];
    }
}
