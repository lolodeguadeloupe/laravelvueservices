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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('pending_balance', 10, 2)->default(0.00); // En attente de validation
            $table->decimal('frozen_balance', 10, 2)->default(0.00); // Gelé pour litiges
            $table->string('currency', 3)->default('EUR');
            $table->json('stripe_account_info')->nullable(); // Infos compte Stripe Connect
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_payout_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
