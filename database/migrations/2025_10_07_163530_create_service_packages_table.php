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
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Ex: "Forfait 3 séances"
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('sessions_count')->default(1); // Nombre de séances incluses
            $table->integer('validity_days')->nullable(); // Validité en jours
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Remise en %
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('conditions')->nullable(); // Conditions spéciales
            $table->timestamps();

            $table->index(['service_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};
