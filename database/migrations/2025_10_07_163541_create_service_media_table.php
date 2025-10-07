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
        Schema::create('service_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('type'); // image, video, document
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size'); // en bytes
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_primary')->default(false); // Image principale
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable(); // dimensions, durée, etc.
            $table->timestamps();

            $table->index(['service_id', 'type', 'is_public']);
            $table->index(['service_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_media');
    }
};
