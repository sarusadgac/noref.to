<?php

namespace App\Models;

use App\Concerns\CreatedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Link extends Model
{
    use CreatedBy, HasFactory;

    protected $fillable = [
        'hash',
        'created_by',
        'scheme',
        'host',
        'port',
        'path',
        'query_string',
        'fragment',
        'url_fingerprint',
    ];

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    /**
     * Reconstruct the full destination URL from stored components.
     */
    protected function destinationUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $url = $this->scheme.'://'.$this->host;

            if ($this->port) {
                $url .= ':'.$this->port;
            }

            if ($this->path) {
                $url .= $this->path;
            }

            if ($this->query_string) {
                $url .= '?'.$this->query_string;
            }

            if ($this->fragment) {
                $url .= '#'.$this->fragment;
            }

            return $url;
        });
    }

    /**
     * Generate a unique 6-character alphanumeric hash.
     */
    public static function generateUniqueHash(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $excludedWords = array_map('strtolower', config('anonto.excluded_words', []));

        do {
            $hash = '';
            for ($i = 0; $i < 6; $i++) {
                $hash .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (
            in_array(strtolower($hash), $excludedWords, true)
            || static::where('hash', $hash)->exists()
        );

        return $hash;
    }

    /**
     * Decompose a URL string into its normalized components.
     *
     * @return array{scheme: string, host: string, port: int|null, path: string|null, query_string: string|null, fragment: string|null}
     *
     * @throws \InvalidArgumentException
     */
    public static function decomposeUrl(string $url): array
    {
        $parts = parse_url($url);

        if ($parts === false) {
            throw new \InvalidArgumentException('Malformed URL provided.');
        }

        $scheme = Str::lower($parts['scheme'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new \InvalidArgumentException('URL must use http or https scheme.');
        }

        if (empty($parts['host'] ?? '')) {
            throw new \InvalidArgumentException('URL must have a valid host.');
        }

        $path = $parts['path'] ?? null;
        if ($path === '/') {
            $path = null;
        }

        return [
            'scheme' => $scheme,
            'host' => Str::lower($parts['host']),
            'port' => $parts['port'] ?? null,
            'path' => $path,
            'query_string' => $parts['query'] ?? null,
            'fragment' => $parts['fragment'] ?? null,
        ];
    }

    /**
     * Compute a SHA-256 fingerprint from URL components.
     */
    public static function computeFingerprint(array $components): string
    {
        $value = implode('|', [
            $components['scheme'],
            $components['host'],
            $components['port'] ?? '',
            $components['path'] ?? '',
            $components['query_string'] ?? '',
            $components['fragment'] ?? '',
        ]);

        return hash('sha256', $value);
    }

    /**
     * Find an existing link by URL fingerprint or create a new one.
     *
     * Uses a retry loop to handle race conditions on both the
     * url_fingerprint unique constraint and the hash unique constraint.
     */
    public static function findOrCreateByUrl(string $url, ?int $createdBy): static
    {
        $components = static::decomposeUrl($url);
        $fingerprint = static::computeFingerprint($components);

        $existing = static::where('url_fingerprint', $fingerprint)->first();

        if ($existing) {
            return $existing;
        }

        if (! $createdBy) {
            $systemUser = User::where('email', config('anonto.system_user_email'))->firstOrFail();
            $createdBy = $systemUser->id;
        }

        $maxAttempts = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return static::create([
                    'hash' => static::generateUniqueHash(),
                    'created_by' => $createdBy,
                    ...$components,
                    'url_fingerprint' => $fingerprint,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Duplicate fingerprint — another request created the same URL concurrently
                if (str_contains($e->getMessage(), 'url_fingerprint')) {
                    return static::where('url_fingerprint', $fingerprint)->firstOrFail();
                }

                // Duplicate hash — retry with a new hash
                if ($attempt === $maxAttempts) {
                    throw $e;
                }
            }
        }

        // @codeCoverageIgnoreStart
        throw new \RuntimeException('Failed to create link after '.$maxAttempts.' attempts.');
        // @codeCoverageIgnoreEnd
    }

    /**
     * Resolve a hash to a destination URL, using cache.
     */
    public static function resolveHash(string $hash): ?string
    {
        $cacheKey = 'link:'.$hash;

        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $link = static::where('hash', $hash)->first();

        if ($link) {
            Cache::put($cacheKey, $link->destination_url, config('anonto.link_cache_ttl', 86400));

            return $link->destination_url;
        }

        return null;
    }

    /**
     * Clear the cache entry for this link.
     */
    public function clearCache(): void
    {
        Cache::forget('link:'.$this->hash);
    }
}
