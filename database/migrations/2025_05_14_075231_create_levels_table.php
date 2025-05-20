<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('student_type_id')->references('id')->on('student_type')->onDelete('cascade');  // Changed reference to 'student_type'
            $table->integer('level_number')->comment('Typically 100-400');
            $table->timestamps();
        });

        // Insert predefined levels for each student type
        // Get IDs for each student type
        $hndId = DB::table('student_type')->where('name', 'HND')->value('id');
        $btechId = DB::table('student_type')->where('name', 'B-Tech')->value('id');
        $topupId = DB::table('student_type')->where('name', 'Top-Up')->value('id');

        // Insert levels for HND (100-300)
        DB::table('levels')->insert([
            ['name' => 'HND Level 100', 'student_type_id' => $hndId, 'level_number' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HND Level 200', 'student_type_id' => $hndId, 'level_number' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HND Level 300', 'student_type_id' => $hndId, 'level_number' => 300, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert levels for B-Tech (100-400)
        DB::table('levels')->insert([
            ['name' => 'B-Tech Level 100', 'student_type_id' => $btechId, 'level_number' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'B-Tech Level 200', 'student_type_id' => $btechId, 'level_number' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'B-Tech Level 300', 'student_type_id' => $btechId, 'level_number' => 300, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'B-Tech Level 400', 'student_type_id' => $btechId, 'level_number' => 400, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert levels for Top-Up (300-400)
        DB::table('levels')->insert([
            ['name' => 'Top-Up Level 300', 'student_type_id' => $topupId, 'level_number' => 300, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Top-Up Level 400', 'student_type_id' => $topupId, 'level_number' => 400, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};