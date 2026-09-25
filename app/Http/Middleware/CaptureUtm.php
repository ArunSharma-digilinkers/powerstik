<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Campaign tags arrive on the landing page and are gone by the time a form is
 * submitted, so they are parked in the session and read back by LeadIntake.
 */
class CaptureUtm
{
    private const KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid'];

    public function handle(Request $request, Closure $next): Response
    {
        $tags = array_filter($request->only(self::KEYS), 'filled');

        if ($tags !== [] && $request->isMethod('GET')) {
            $request->session()->put('utm', array_map(
                fn ($value) => Str::limit((string) $value, 120, ''),
                $tags,
            ));
        }

        return $next($request);
    }
}
