<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from URL parameter, session, or default
        $locale = $request->route('locale')
            ?? session('locale')
            ?? config('app.locale', 'ar');

        // Validate locale
        if (!in_array($locale, array_keys(config('app.locales', ['ar', 'en'])))) {
            $locale = config('app.locale', 'ar');
        }

        // Set application locale
        app()->setLocale($locale);

        // Store in session
        session(['locale' => $locale]);

        return $next($request);
    }
}
