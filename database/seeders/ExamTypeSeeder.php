<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamTypeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $examTypes = [
            [
                'name' => 'Final Exam',
                'description' => 'Final examination for the course'
            ],
            [
                'name' => 'Resit Exam',
                'description' => 'Resit examination for students who failed or missed the final exam'
            ],
        ];

        foreach ($examTypes as $type) {
            DB::table('exam_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}