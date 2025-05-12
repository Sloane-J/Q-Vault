<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'department_id',
        'semester',
        'exam_type',
        'course_name',
        'exam_year',
        'student_type_id',
        'level_id',
        'visibility',
        'user_id'
    ];

    // Add relationships if needed
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}