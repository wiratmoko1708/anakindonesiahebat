<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->userName().'@gmail.com',
            'email_verified_at' => now(),
            'google_id' => (string) fake()->unique()->numerify('##################'),
            'role' => Role::User,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => ['email_verified_at' => null]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::SuperAdmin]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::Admin]);
    }

    public function withClass(?SchoolClass $schoolClass = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'school_class_id' => $schoolClass?->id ?? SchoolClass::factory(),
        ]);
    }
}
