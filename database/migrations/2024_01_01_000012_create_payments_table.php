<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('stripe'); // stripe, square, etc
            $table->string('intent_id')->nullable(); // Stripe Payment Intent ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'processing', 'succeeded', 'failed', 'cancelled', 'refunded']);
            $table->string('method')->nullable(); // card, apple_pay, google_pay
            $table->json('metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['intent_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
