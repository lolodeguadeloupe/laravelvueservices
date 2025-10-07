<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class BadgeCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badges:check 
                           {--user= : ID de l\'utilisateur spécifique à vérifier}
                           {--create-defaults : Créer les badges par défaut}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier et attribuer automatiquement les badges aux utilisateurs éligibles';

    /**
     * Execute the console command.
     */
    public function handle(BadgeService $badgeService)
    {
        if ($this->option('create-defaults')) {
            $this->info('Création des badges par défaut...');
            $badgeService->createDefaultBadges();
            $this->info('✅ Badges par défaut créés avec succès.');

            return 0;
        }

        $userId = $this->option('user');

        if ($userId) {
            return $this->checkSingleUser($userId, $badgeService);
        }

        return $this->checkAllUsers($badgeService);
    }

    private function checkSingleUser(int $userId, BadgeService $badgeService): int
    {
        $user = User::find($userId);

        if (! $user) {
            $this->error("Utilisateur avec l'ID {$userId} non trouvé.");

            return 1;
        }

        $this->info("Vérification des badges pour {$user->name} (ID: {$user->id})...");

        $earnedBadges = $badgeService->checkAndAwardBadges($user);

        if ($earnedBadges->isEmpty()) {
            $this->info('Aucun nouveau badge attribué.');
        } else {
            $this->info("✅ {$earnedBadges->count()} nouveau(x) badge(s) attribué(s) :");

            foreach ($earnedBadges as $userBadge) {
                $badge = $userBadge->badge;
                $this->line("   🏆 {$badge->name} ({$badge->rarity_label})");
            }
        }

        return 0;
    }

    private function checkAllUsers(BadgeService $badgeService): int
    {
        $this->info('Vérification des badges pour tous les utilisateurs...');

        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $stats = [
            'checked' => 0,
            'badges_awarded' => 0,
            'users_with_new_badges' => 0,
        ];

        User::providers()->active()->chunk(100, function ($users) use (&$stats, $badgeService, $progressBar) {
            foreach ($users as $user) {
                $stats['checked']++;
                $progressBar->advance();

                $earnedBadges = $badgeService->checkAndAwardBadges($user);

                if ($earnedBadges->isNotEmpty()) {
                    $stats['badges_awarded'] += $earnedBadges->count();
                    $stats['users_with_new_badges']++;
                }
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        $this->info('✅ Vérification terminée :');
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Utilisateurs vérifiés', $stats['checked']],
                ['Badges attribués', $stats['badges_awarded']],
                ['Utilisateurs avec nouveaux badges', $stats['users_with_new_badges']],
            ]
        );

        return 0;
    }
}
