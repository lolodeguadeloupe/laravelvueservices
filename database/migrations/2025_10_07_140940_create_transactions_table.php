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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // commission, payout, refund, fee
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('stripe_transfer_id')->nullable();
            $table->json('metadata')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
