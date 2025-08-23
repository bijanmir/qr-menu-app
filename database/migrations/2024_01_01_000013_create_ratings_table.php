<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('device_hash')->nullable(); // for anonymous ratings
            $table->tinyInteger('stars')->unsigned(); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('verified')->default(true); // verified purchaser
            $table->timestamps();

            $table->unique(['order_item_id', 'user_id']);
            $table->unique(['order_item_id', 'device_hash']);
            $table->index(['item_id', 'verified']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
};
