<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get IDs for each student type
        $hndId = DB::table('student_type')->where('name', 'HND')->value('id');
        $btechId = DB::table('student_type')->where('name', 'B-Tech')->value('id');
        $topupId = DB::table('student_type')->where('name', 'Top-Up')->value('id');

        $levels = [
            // HND Levels (100-300)
            ['name' => 'HND Level 100', 'student_type_id' => $hndId, 'level_number' => 100],
            ['name' => 'HND Level 200', 'student_type_id' => $hndId, 'level_number' => 200],
            ['name' => 'HND Level 300', 'student_type_id' => $hndId, 'level_number' => 300],
            
            // B-Tech Levels (100-400)
            ['name' => 'B-Tech Level 100', 'student_type_id' => $btechId, 'level_number' => 100],
            ['name' => 'B-Tech Level 200', 'student_type_id' => $btechId, 'level_number' => 200],
            ['name' => 'B-Tech Level 300', 'student_type_id' => $btechId, 'level_number' => 300],
            ['name' => 'B-Tech Level 400', 'student_type_id' => $btechId, 'level_number' => 400],
            
            // Top-Up Levels (300-400)
            ['name' => 'Top-Up Level 300', 'student_type_id' => $topupId, 'level_number' => 300],
            ['name' => 'Top-Up Level 400', 'student_type_id' => $topupId, 'level_number' => 400],
        ];

        foreach ($levels as $level) {
            DB::table('levels')->updateOrInsert(
                [
                    'name' => $level['name'],
                    'student_type_id' => $level['student_type_id'],
                    'level_number' => $level['level_number']
                ],
                [
                    'name' => $level['name'],
                    'student_type_id' => $level['student_type_id'],
                    'level_number' => $level['level_number'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}