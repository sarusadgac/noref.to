<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Link;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'link_id' => Link::factory(),
            'email' => fake()->safeEmail(),
            'comment' => fake()->sentence(),
            'status' => ReportStatus::Pending,
        ];
    }

    /**
     * Indicate that the report has been resolved.
     */
    public function resolved(?User $resolver = null): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Resolved,
            'resolved_by' => $resolver?->id ?? User::factory()->admin(),
            'resolved_at' => now(),
        ]);
    }

    /**
     * Indicate that the report has been dismissed.
     */
    public function dismissed(?User $resolver = null): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Dismissed,
            'resolved_by' => $resolver?->id ?? User::factory()->admin(),
            'resolved_at' => now(),
        ]);
    }
}
