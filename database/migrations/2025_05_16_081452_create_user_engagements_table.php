<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('user_engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->dateTime('login_at');
            $table->dateTime('logout_at')->nullable();
            $table->dateTime('last_activity_at');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('pages_visited')->nullable();
            $table->json('actions_performed')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'login_at']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_engagements');
    }
};