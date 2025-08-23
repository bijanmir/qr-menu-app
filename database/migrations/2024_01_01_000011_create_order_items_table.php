<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // snapshot at time of order
            $table->decimal('price', 8, 2); // snapshot at time of order
            $table->integer('quantity')->default(1);
            $table->json('modifiers')->nullable(); // selected modifiers with prices
            $table->text('notes')->nullable();
            $table->string('prep_station')->nullable();
            $table->enum('status', ['pending', 'in_prep', 'ready', 'served'])->default('pending');
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
