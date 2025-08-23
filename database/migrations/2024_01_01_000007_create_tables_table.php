<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('code'); // Table number/code
            $table->integer('seats')->default(4);
            $table->string('area')->nullable(); // dining room, patio, bar
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['restaurant_id', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tables');
    }
};
