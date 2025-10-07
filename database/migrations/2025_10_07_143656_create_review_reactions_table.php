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
        Schema::create('review_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // helpful, not_helpful, report
            $table->text('reason')->nullable(); // Pour les signalements
            $table->timestamps();

            // Un utilisateur ne peut réagir qu'une fois par avis
            $table->unique(['review_id', 'user_id']);
            $table->index(['review_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_reactions');
    }
};
