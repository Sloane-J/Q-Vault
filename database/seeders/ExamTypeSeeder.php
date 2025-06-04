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
                // Regular Students Final Exams
                'name' => 'Final Exam',
                'description' => 'Final examination for the course'
            ],
            [   // Regular Students Resit Exams
                'name' => 'Resit Exam',
                'description' => 'Resit examination for students who failed or missed the final exam'
            ],
            [ //B-Tech Weekend Final Exams
                'name' => 'B-Tech Weekend Final Exam',
                'description' => 'Final examination for B-Tech weekend students'
            ],
            [ //B-Tech Weekend Resit Exams
                'name' => 'B-Tech Weekend Resit Exam',
                'description' => 'Resit examination for B-Tech weekend students who failed or missed the final exam'
            ],
            [ //DBS Final Exams
                'name' => 'DBS Final Exam',
                'description' => 'Final examination for DBS students'
            ],
            [ //DBS Resit Exams
                'name' => 'DBS Resit Exam',
                'description' => 'Resit examination for DBS students who failed or missed the final exam'

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