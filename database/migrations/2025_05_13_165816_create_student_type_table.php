<?php

use Illuminate\Database\Migrations\Migration; 
use Illuminate\Database\Schema\Blueprint; 
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_type', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert predefined student types
        DB::table('student_type')->insert([
            ['name' => 'HND', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'B-Tech', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Top-Up', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_type');
    }
};