<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    /**
     * Relationship with courses
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Relationship with users (department admins)
     */
    public function admins()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Relationship with exam papers
     */
    public function examPapers()
    {
        return $this->hasMany(ExamPaper::class);
    }
}