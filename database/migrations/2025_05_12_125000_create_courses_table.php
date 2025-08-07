<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique(); // Keep if needed, else remove
                $table->foreignId('department_id')->constrained()->onDelete('cascade');
                $table->text('description')->nullable();
                $table->boolean('active')->default(true); // Renamed from is_active
                $table->timestamps();
                $table->softDeletes();
                    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};