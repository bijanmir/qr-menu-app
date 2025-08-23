<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menu_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linked_menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('source_menu_id')->constrained('menus')->cascadeOnDelete();
            $table->enum('propagation_mode', ['immediate', 'manual', 'scheduled'])->default('manual');
            $table->json('override_fields')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['linked_menu_id', 'source_menu_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_links');
    }
};
