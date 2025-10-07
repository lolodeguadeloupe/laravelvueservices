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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_id')->constrained('users')->onDelete('cascade');
            $table->string('reviewer_type'); // client, provider
            $table->integer('overall_rating')->unsigned(); // 1-5
            
            // Critères détaillés (1-5 chacun)
            $table->integer('quality_rating')->unsigned()->nullable();
            $table->integer('communication_rating')->unsigned()->nullable();
            $table->integer('punctuality_rating')->unsigned()->nullable();
            $table->integer('professionalism_rating')->unsigned()->nullable();
            $table->integer('value_rating')->unsigned()->nullable(); // rapport qualité-prix
            
            $table->string('title')->nullable();
            $table->text('comment');
            $table->json('photos')->nullable();
            
            // Réponse du professionnel
            $table->text('response')->nullable();
            $table->timestamp('response_at')->nullable();
            
            // Modération
            $table->string('status')->default('pending'); // pending, approved, rejected, reported
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->text('moderation_reason')->nullable();
            
            // Vérification
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            
            // Utilité
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            $table->timestamps();

            // Index pour performance
            $table->index(['reviewed_id', 'status', 'overall_rating']);
            $table->index(['reviewer_id', 'created_at']);
            $table->index(['booking_request_id']);
            $table->index(['status', 'is_verified', 'created_at']);
            
            // Note: Les contraintes CHECK seront ajoutées via une migration séparée si nécessaire
            // car elles ne sont pas supportées de manière uniforme sur toutes les bases de données
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
