<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.turnstile.secret_key');

        if (! $secretKey) {
            return;
        }

        try {
            $response = Http::timeout(5)->asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            report($e);
            $fail('Security verification is temporarily unavailable. Please try again shortly.');

            return;
        }

        if ($response->failed()) {
            report(new \RuntimeException('Turnstile API returned HTTP '.$response->status()));
            $fail('Security verification is temporarily unavailable. Please try again shortly.');

            return;
        }

        if (! ($response->json('success') ?? false)) {
            $fail('Turnstile verification failed. Please try again.');
        }
    }
}
