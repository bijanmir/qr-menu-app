<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('image_path')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['restaurant_id', 'active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('qr_codes');
    }
};
