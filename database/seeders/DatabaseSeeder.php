<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SchoolClassSeeder::class,
            QuestionSeeder::class,
        ]);

        foreach (config('kaih.super_admin_emails') as $email) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => str($email)->before('@')->headline()->toString(), 'role' => Role::SuperAdmin],
            );
        }
    }
}
