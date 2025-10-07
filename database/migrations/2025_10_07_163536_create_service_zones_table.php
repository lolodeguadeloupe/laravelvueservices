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
        Schema::create('service_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Ex: "Paris et petite couronne"
            $table->text('description')->nullable();
            $table->string('type')->default('radius'); // radius, postal_codes, custom
            $table->decimal('latitude', 10, 8)->nullable(); // Point central
            $table->decimal('longitude', 11, 8)->nullable(); // Point central
            $table->integer('radius_km')->nullable(); // Rayon en km
            $table->json('postal_codes')->nullable(); // Codes postaux couverts
            $table->json('excluded_areas')->nullable(); // Zones exclues
            $table->decimal('travel_cost_per_km', 8, 2)->nullable(); // Coût déplacement
            $table->integer('min_travel_time')->nullable(); // Temps de déplacement min
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_id', 'is_active']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_zones');
    }
};
