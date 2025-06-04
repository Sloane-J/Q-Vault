<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('AdminPassword123!'),
                'role' => 'admin'
            ],
            [
                'name' => 'Sloane Jnr',
                'email' => 'sloane.jnr@qvault.com',
                'password' => Hash::make('SloanePassword123!'),
                'role' => 'admin'
            ],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@qvault.com',
                'password' => Hash::make('SuperAdminPassword123!'),
                'role' => 'admin'
            ]
        ];

        foreach ($admins as $adminData) {
            // Check if admin already exists to prevent duplicates
            if (!User::where('email', $adminData['email'])->exists()) {
                User::create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'email_verified_at' => now(),
                    'password' => $adminData['password'],
                    'role' => $adminData['role']
                ]);
            }
        }
    }
}