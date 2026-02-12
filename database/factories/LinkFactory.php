<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheme = fake()->randomElement(['http', 'https']);
        $host = fake()->domainName();
        $path = '/'.fake()->slug(3);
        $components = [
            'scheme' => $scheme,
            'host' => $host,
            'port' => null,
            'path' => $path,
            'query_string' => null,
            'fragment' => null,
        ];

        return [
            'hash' => Link::generateUniqueHash(),
            'created_by' => User::factory(),
            ...$components,
            'url_fingerprint' => Link::computeFingerprint($components),
        ];
    }

    /**
     * Create a link from a specific URL.
     */
    public function withUrl(string $url): static
    {
        $components = Link::decomposeUrl($url);

        return $this->state(fn (array $attributes) => [
            ...$components,
            'url_fingerprint' => Link::computeFingerprint($components),
        ]);
    }

    /**
     * Create an anonymous link owned by the system user.
     */
    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => User::where('email', config('anonto.system_user_email'))->first()?->id
                ?? User::factory()->system(),
        ]);
    }
}
