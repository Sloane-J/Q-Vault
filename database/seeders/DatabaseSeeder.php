<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'role' => 'student',
                'password' => Hash::make('password'),
            ]
        );

        // Create admin user (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => Hash::make('AdminPassword123!'), // Change this to a secure password
            ]
        );

        // Create additional test students (only if they don't exist)
        if (User::where('role', 'student')->count() < 7) { // 1 test user + 5 additional = 6, so if less than 7
            User::factory(5)->create([
                'role' => 'student'
            ]);
        }

        $this->call([
            StudentTypeSeeder::class,
            LevelSeeder::class,
        ]);
    }
}