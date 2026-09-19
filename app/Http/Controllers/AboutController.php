<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use App\Models\TeamMember;
use App\Models\TimelineEvent;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Fixed About page photography. Drop optimised files at these paths under public/
     * and they appear; until then the design's tinted wells show.
     */
    private const MEDIA = [
        'design_india' => 'media/about/design-india.webp',
        'team_design' => 'media/about/team-design.webp',
        'team_sales' => 'media/about/team-sales.webp',
        'team_production' => 'media/about/team-production.webp',
        'team_quality' => 'media/about/team-quality.webp',
        'plant_hall' => 'media/about/plant-hall.webp',
        'plant_corrugation' => 'media/about/plant-corrugation.webp',
        'plant_labels' => 'media/about/plant-labels.webp',
        'sustainability' => 'media/about/sustainability.webp',
        'careers' => 'media/about/careers.webp',
    ];

    public function __invoke(Request $request): View
    {
        $milestones = TimelineEvent::ordered()->get();

        // ?year=2017 preselects that milestone so the timeline can be shared
        $active = max(0, (int) $milestones->search(fn ($m) => (string) $m->year === $request->query('year')));

        return view('about', [
            'milestones' => $milestones,
            'active' => $active,
            'leaders' => TeamMember::published()->where('is_leadership', true)->ordered()->limit(2)->get(),
            'roles' => JobOpening::published()->ordered()->get(),
            'certifications' => $this->lines(Site::setting('certifications')),
            'sustainability' => array_map(fn ($line) => array_map('trim', explode(':', $line, 2)) + [1 => ''], $this->lines(Site::setting('sustainability'))),
            'media' => array_map(fn ($path) => is_file(public_path($path)) ? asset($path) : null, self::MEDIA),
        ]);
    }

    /** @return list<string> */
    private function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text))));
    }
}
