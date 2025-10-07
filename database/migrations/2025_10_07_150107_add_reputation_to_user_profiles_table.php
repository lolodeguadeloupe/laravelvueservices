<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('reputation_points')->default(0)->after('rating');
            $table->json('badge_counts')->nullable()->after('reputation_points'); // Cache des compteurs de badges
            $table->timestamp('last_badge_check')->nullable()->after('badge_counts');

            // Index pour les requêtes de classement
            $table->index('reputation_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropIndex(['reputation_points']);
            $table->dropColumn(['reputation_points', 'badge_counts', 'last_badge_check']);
        });
    }
};
