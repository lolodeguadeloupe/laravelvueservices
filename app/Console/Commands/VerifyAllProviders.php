<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyAllProviders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'provider:verify-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify all providers for demo/development purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $pendingProviders = User::where('user_type', 'provider')
                ->where('verification_status', 'pending')
                ->get();

            if ($pendingProviders->isEmpty()) {
                $this->info('No pending providers found.');

                return 0;
            }

            $this->info("Found {$pendingProviders->count()} pending provider(s). Verifying...");

            foreach ($pendingProviders as $provider) {
                $provider->update([
                    'verification_status' => 'verified',
                    'verified_at' => now(),
                ]);

                $this->line("✅ Verified: {$provider->name} ({$provider->email})");
            }

            $this->info('');
            $this->info("🎉 Successfully verified {$pendingProviders->count()} provider(s)!");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }
    }
}
