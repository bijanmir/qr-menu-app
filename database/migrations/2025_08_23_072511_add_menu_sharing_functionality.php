<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add sharing columns to items table
        Schema::table('items', function (Blueprint $table) {
            // For tracking item relationships
            $table->enum('sharing_type', ['original', 'duplicated', 'linked'])->default('original')->after('id');
            $table->foreignId('source_item_id')->nullable()->constrained('items')->nullOnDelete()->after('sharing_type');
            $table->foreignId('source_restaurant_id')->nullable()->constrained('restaurants')->nullOnDelete()->after('source_item_id');
            
            // Override settings for duplicated/linked items
            $table->boolean('allow_price_override')->default(true)->after('price');
            $table->boolean('allow_description_override')->default(true)->after('description');
            $table->boolean('allow_image_override')->default(true)->after('image');
            
            // Tracking
            $table->timestamp('last_synced_at')->nullable()->after('updated_at');
            $table->json('sync_settings')->nullable()->after('last_synced_at'); // What fields to sync for linked items
            
            $table->index(['sharing_type', 'source_item_id']);
            $table->index(['source_restaurant_id', 'sharing_type']);
        });

        // Create item sharing history table for auditing
        Schema::create('item_sharing_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('target_item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('source_restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('target_restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->enum('action_type', ['duplicated', 'linked', 'synced', 'unlinked']);
            $table->json('changed_fields')->nullable(); // What was changed during sync
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who performed the action
            $table->timestamps();

            $table->index(['source_item_id', 'action_type']);
            $table->index(['target_item_id', 'action_type']);
        });

        // Add sharing settings to restaurants table
        Schema::table('restaurants', function (Blueprint $table) {
            $table->json('sharing_settings')->nullable()->after('active');
            // Will contain settings like:
            // {
            //   "allow_incoming_duplications": true,
            //   "allow_incoming_links": true,
            //   "allow_outgoing_sharing": true,
            //   "auto_approve_sharing": false,
            //   "sharing_permissions": ["same_tenant", "public"]
            // }
        });

        // Create sharing requests table for approval workflow
        Schema::create('item_sharing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('source_restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('target_restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->enum('sharing_type', ['duplicate', 'link']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->text('message')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index(['target_restaurant_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_sharing_requests');
        Schema::dropIfExists('item_sharing_history');
        
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('sharing_settings');
        });
        
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['source_item_id']);
            $table->dropForeign(['source_restaurant_id']);
            $table->dropColumn([
                'sharing_type',
                'source_item_id', 
                'source_restaurant_id',
                'allow_price_override',
                'allow_description_override', 
                'allow_image_override',
                'last_synced_at',
                'sync_settings'
            ]);
        });
    }
};
