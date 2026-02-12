<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Domain extends Model
{
    /** @use HasFactory<\Database\Factories\DomainFactory> */
    use HasFactory;

    protected $fillable = [
        'host',
        'is_allowed',
        'reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_allowed' => 'boolean',
        ];
    }

    /**
     * Check if a domain host is allowed, blocked, or unknown.
     *
     * Returns true if allowed, false if blocked, null if not in the table.
     */
    public static function isAllowed(string $host): ?bool
    {
        $host = strtolower($host);

        return Cache::remember("domain:{$host}", 3600, function () use ($host) {
            $candidates = [$host];

            $parts = explode('.', $host);
            while (count($parts) > 2) {
                array_shift($parts);
                $candidates[] = implode('.', $parts);
            }

            $domain = static::whereIn('host', $candidates)
                ->orderByRaw('host = ? DESC', [$host])
                ->first();

            return $domain?->is_allowed;
        });
    }

    /**
     * Clear the cache entry for this domain.
     */
    public function clearCache(): void
    {
        Cache::forget("domain:{$this->host}");
    }
}
