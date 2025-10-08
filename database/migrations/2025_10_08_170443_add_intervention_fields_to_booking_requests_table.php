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
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('intervention_report')->nullable(); // photos, description, matériaux utilisés
            $table->json('client_signature')->nullable(); // signature numérique
            $table->json('provider_location')->nullable(); // GPS du prestataire
            $table->text('work_summary')->nullable();
            $table->json('before_photos')->nullable();
            $table->json('after_photos')->nullable();
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->dropColumn([
                'started_at', 'finished_at', 'intervention_report', 'client_signature',
                'provider_location', 'work_summary', 'before_photos', 'after_photos',
                'requires_follow_up', 'follow_up_date'
            ]);
        });
    }
};
