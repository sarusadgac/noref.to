<?php

namespace Database\Factories;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Domain>
 */
class DomainFactory extends Factory
{
    protected $model = Domain::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'host' => fake()->unique()->domainName(),
            'is_allowed' => true,
            'reason' => null,
        ];
    }

    /**
     * Indicate that the domain is allowed.
     */
    public function allowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_allowed' => true,
        ]);
    }

    /**
     * Indicate that the domain is blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_allowed' => false,
        ]);
    }
}
