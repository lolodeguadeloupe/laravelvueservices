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
        Schema::table('reviews', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà avant de les ajouter
            if (! Schema::hasColumn('reviews', 'auto_moderation_flags')) {
                $table->json('auto_moderation_flags')->nullable()->after('moderation_reason');
            }
            if (! Schema::hasColumn('reviews', 'report_count')) {
                $table->integer('report_count')->default(0)->after('auto_moderation_flags');
            }
            if (! Schema::hasColumn('reviews', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('report_count');
            }
            if (! Schema::hasColumn('reviews', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_published');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'moderation_status',
                'moderated_at',
                'moderated_by',
                'moderation_reason',
                'auto_moderation_flags',
                'report_count',
                'is_published',
                'published_at',
            ]);
        });
    }
};
