<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modifier_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Size, Add-ons, etc.
            $table->boolean('required')->default(false);
            $table->integer('min_selection')->default(0);
            $table->integer('max_selection')->nullable();
            $table->integer('sort_index')->default(0);
            $table->timestamps();

            $table->index(['item_id', 'sort_index']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('modifier_groups');
    }
};
