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
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at');
            $table->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete(); // Attribution manuelle
            $table->text('reason')->nullable(); // Raison de l'attribution
            $table->json('context')->nullable(); // Contexte (réservation, avis, etc.)
            $table->boolean('is_featured')->default(false); // Affiché en premier
            $table->boolean('is_public')->default(true); // Visible publiquement
            $table->timestamps();

            // Contrainte unique : un utilisateur ne peut avoir qu'une fois le même badge
            $table->unique(['user_id', 'badge_id']);

            // Index
            $table->index(['user_id', 'earned_at']);
            $table->index(['badge_id', 'earned_at']);
            $table->index(['user_id', 'is_featured', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
