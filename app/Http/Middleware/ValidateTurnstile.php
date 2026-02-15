<?php

namespace App\Http\Middleware;

use App\Rules\Turnstile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateTurnstile
{
    /** @var array<int, string> */
    protected array $protectedRoutes = [
        'login.store',
        'register.store',
        'password.email',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('POST') || ! config('services.turnstile.secret_key')) {
            return $next($request);
        }

        if (! in_array($request->route()?->getName(), $this->protectedRoutes)) {
            return $next($request);
        }

        $validator = Validator::make($request->only('cf-turnstile-response'), [
            'cf-turnstile-response' => ['required', new Turnstile],
        ], [
            'cf-turnstile-response.required' => __('Security verification failed. Please refresh the page and try again.'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        return $next($request);
    }
}
