<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable();
            $table->string('timezone')->default('UTC');
            $table->text('address')->nullable();
            $table->json('open_hours')->nullable();
            $table->json('service_modes')->default('["dine-in"]'); // dine-in, takeout, delivery
            $table->json('table_map')->nullable();
            $table->json('printer_config')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
};
