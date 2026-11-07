<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'school_class_id' => SchoolClass::factory(),
            'attempt_number' => 1,
            'average_score' => fake()->randomFloat(2, 1, 4),
            'reflection' => fake()->optional()->sentence(),
            'submitted_at' => now(),
        ];
    }
}
