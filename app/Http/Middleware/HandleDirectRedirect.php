<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class HandleDirectRedirect
{
    /**
     * Intercept GET / when query string is a valid URL for direct redirect.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('/') && $request->isMethod('GET')) {
            $queryString = rawurldecode($request->server('QUERY_STRING', ''));

            if ($queryString && filter_var($queryString, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $queryString)) {
                Cache::increment('direct_redirects:total');
                Cache::increment('direct_redirects:'.now()->toDateString());

                $host = parse_url($queryString, PHP_URL_HOST);
                $domainStatus = $host ? Domain::isAllowed($host) : null;

                return response()->view('interstitial', [
                    'url' => $queryString,
                    'autoRedirect' => $domainStatus === true,
                    'blocked' => $domainStatus === false,
                ])
                    ->header('Cache-Control', 'no-store');
            }
        }

        return $next($request);
    }
}
