<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Link;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends Controller
{
    /**
     * Handle the incoming redirect request.
     */
    public function __invoke(string $hash): Response
    {
        $destinationUrl = Link::resolveHash($hash);

        if (! $destinationUrl) {
            abort(404);
        }

        $host = parse_url($destinationUrl, PHP_URL_HOST);
        $domainStatus = $host ? Domain::isAllowed($host) : null;

        return response()
            ->view('interstitial', [
                'url' => $destinationUrl,
                'autoRedirect' => $domainStatus === true,
                'blocked' => $domainStatus === false,
            ])
            ->header('Cache-Control', 'no-store');
    }
}
