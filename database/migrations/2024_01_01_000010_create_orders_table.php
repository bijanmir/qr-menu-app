<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'pending', 'accepted', 'in_kitchen', 'ready', 
                'served', 'paid', 'cancelled'
            ])->default('pending');
            $table->enum('channel', ['dine-in', 'takeout', 'delivery'])->default('dine-in');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('service_fees', 10, 2)->default(0);
            $table->decimal('tips', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_status', ['pending', 'processing', 'paid', 'failed', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('customer_info')->nullable(); // name, phone for takeout
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'status', 'created_at']);
            $table->index(['order_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
