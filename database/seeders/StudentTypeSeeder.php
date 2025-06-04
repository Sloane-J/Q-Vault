<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentTypeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $studentTypes = [
            ['name' => 'HND'],
            ['name' => 'B-Tech'],
            ['name' => 'Top-Up'],
            ['name' => 'DBS'],
            ['name' => 'MTech'],


        ];

        foreach ($studentTypes as $type) {
            DB::table('student_type')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'name' => $type['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}