<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignClientRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-client-role {email : The email of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign client role to a user and ensure roles exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        try {
            // Create roles if they don't exist
            $this->ensureRolesExist();

            // Find user
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->error("User with email {$email} not found.");

                return 1;
            }

            // Remove any existing roles and assign client role
            $user->syncRoles(['client']);

            $this->info("Successfully assigned 'client' role to user: {$user->name} ({$user->email})");

            // Show user's current roles
            $roles = $user->getRoleNames();
            $this->info('User now has roles: '.$roles->implode(', '));

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }
    }

    private function ensureRolesExist()
    {
        $roles = ['client', 'provider', 'admin'];

        foreach ($roles as $roleName) {
            if (! Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $this->info("Created role: {$roleName}");
            }
        }
    }
}
