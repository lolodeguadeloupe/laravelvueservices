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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('price_type')->default('fixed'); // fixed, hourly, custom
            $table->integer('duration')->nullable(); // in minutes
            $table->json('location')->nullable(); // service area
            $table->json('images')->nullable();
            $table->json('requirements')->nullable(); // what client needs to provide
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('bookings_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
