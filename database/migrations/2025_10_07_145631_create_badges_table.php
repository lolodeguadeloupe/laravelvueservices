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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Couleur hexa
            $table->enum('type', ['achievement', 'certification', 'milestone', 'quality']);
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary']);
            $table->json('criteria'); // Critères d'attribution automatique
            $table->integer('points')->default(0); // Points de réputation
            $table->boolean('is_active')->default(true);
            $table->boolean('is_automatic')->default(true); // Attribution automatique
            $table->integer('display_order')->default(0);
            $table->timestamps();

            // Index
            $table->index(['type', 'is_active']);
            $table->index(['rarity', 'is_active']);
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
