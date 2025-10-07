<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            CategorySeeder::class,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'user_type' => 'admin',
        ]);
        $admin->assignRole('admin');

        // Create test provider
        $provider = User::factory()->create([
            'name' => 'Test Provider',
            'email' => 'provider@example.com',
            'user_type' => 'provider',
        ]);
        $provider->assignRole('provider');

        // Create test client
        $client = User::factory()->create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'user_type' => 'client',
        ]);
        $client->assignRole('client');

        $this->command->info('Database seeded successfully!');
    }
}
