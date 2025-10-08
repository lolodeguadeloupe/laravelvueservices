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
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_connect_id')->nullable(); // Compte Stripe Connect
            $table->string('stripe_connect_status')->nullable(); // pending, active, restricted
            $table->json('stripe_connect_requirements')->nullable(); // Exigences manquantes
            $table->timestamp('stripe_connect_verified_at')->nullable();
            $table->decimal('total_earnings', 10, 2)->default(0.00);
            $table->decimal('available_balance', 10, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_connect_id',
                'stripe_connect_status', 
                'stripe_connect_requirements',
                'stripe_connect_verified_at',
                'total_earnings',
                'available_balance'
            ]);
        });
    }
};
