<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('sku')->nullable();
            $table->string('image')->nullable();
            $table->json('allergens')->nullable();
            $table->json('tags')->nullable(); // ["Most Popular", "Spicy"]
            $table->integer('calories')->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('available')->default(true); // 86ing toggle
            $table->string('tax_code')->nullable();
            $table->string('prep_station')->nullable();
            $table->integer('sort_index')->default(0);
            $table->timestamps();

            $table->index(['menu_id', 'visible', 'available']);
            $table->index(['category_id', 'sort_index']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
};
