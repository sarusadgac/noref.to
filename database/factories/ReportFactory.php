<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Link;
use App\Models\Report;
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
}
