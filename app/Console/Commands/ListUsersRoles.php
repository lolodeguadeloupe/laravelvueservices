<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsersRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users and their assigned roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $users = User::with('roles')->get();

            if ($users->isEmpty()) {
                $this->info('No users found in the database.');

                return 0;
            }

            $this->info('Users and their roles:');
            $this->line('');

            foreach ($users as $user) {
                $roles = $user->getRoleNames();
                $rolesList = $roles->isNotEmpty() ? $roles->implode(', ') : 'No roles assigned';

                $this->line("📧 {$user->email}");
                $this->line("   Name: {$user->name}");
                $this->line("   Type: {$user->user_type}");
                $this->line("   Roles: {$rolesList}");
                $this->line('   Verified: '.($user->email_verified_at ? 'Yes' : 'No'));
                $this->line('');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }
    }
}
