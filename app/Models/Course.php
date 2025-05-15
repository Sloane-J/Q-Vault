<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'department_id',
        'active'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function studentType()
    {
        return $this->belongsTo(StudentType::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }
}