<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('sort_index')->default(0);
            $table->string('icon')->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();

            $table->index(['menu_id', 'visible', 'sort_index']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
