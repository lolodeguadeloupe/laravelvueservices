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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // for public references
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, accepted, rejected, completed, cancelled
            $table->datetime('preferred_datetime');
            $table->datetime('confirmed_datetime')->nullable();
            $table->json('client_address'); // where service will be performed
            $table->text('client_notes')->nullable();
            $table->text('provider_notes')->nullable();
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->integer('estimated_duration')->nullable(); // in minutes
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
