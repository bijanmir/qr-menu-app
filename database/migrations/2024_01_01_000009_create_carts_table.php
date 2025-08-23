<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('items'); // cart items with modifiers
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('service_fees', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'restaurant_id']);
            $table->index(['user_id', 'restaurant_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
