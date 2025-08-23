<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modifier_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price_adjustment', 8, 2)->default(0);
            $table->boolean('available')->default(true);
            $table->integer('sort_index')->default(0);
            $table->timestamps();

            $table->index(['modifier_group_id', 'available', 'sort_index']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('modifiers');
    }
};
