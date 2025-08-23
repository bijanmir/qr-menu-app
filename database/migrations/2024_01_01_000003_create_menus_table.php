<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('menu_scope', ['RestaurantLocal', 'GlobalTemplate'])->default('RestaurantLocal');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
            $table->json('schedule')->nullable();
            $table->json('theme')->nullable();
            $table->datetime('published_at')->nullable();
            $table->datetime('scheduled_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'status']);
            $table->index(['tenant_id', 'menu_scope']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
