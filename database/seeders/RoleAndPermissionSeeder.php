<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Service management
            'manage services',
            'view services',
            'create services',
            'edit services',
            'delete services',

            // Booking management
            'manage bookings',
            'view bookings',
            'accept bookings',
            'reject bookings',
            'complete bookings',
            'cancel bookings',

            // User management
            'manage users',
            'view users',
            'edit users',
            'delete users',
            'verify providers',

            // Category management
            'manage categories',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Payment management
            'manage payments',
            'view payments',
            'process refunds',

            // Admin panel access
            'access admin panel',
            'view analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Client role
        $clientRole = Role::create(['name' => 'client']);
        $clientRole->givePermissionTo([
            'view services',
            'view bookings',
            'cancel bookings',
        ]);

        // Provider role
        $providerRole = Role::create(['name' => 'provider']);
        $providerRole->givePermissionTo([
            'view services',
            'create services',
            'edit services',
            'delete services',
            'view bookings',
            'accept bookings',
            'reject bookings',
            'complete bookings',
            'cancel bookings',
        ]);

        // Admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $this->command->info('Roles and permissions created successfully!');
    }
}
