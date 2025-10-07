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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('provider_amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('pending'); // pending, processing, completed, failed, refunded
            $table->string('payment_method')->nullable(); // card, bank_transfer, etc.
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->json('stripe_metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['client_id', 'status']);
            $table->index(['provider_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
