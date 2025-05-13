<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Note: We don't have an exam_types table in migrations,
    // This is more of a helper model for validation/selection
    // You could replace this with an enum or config array
    
    public static function getTypes(): array
    {
        return [
            'final' => 'Final Exam',
            'resit' => 'Resit Exam'
        ];
    }
}