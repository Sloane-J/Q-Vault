<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'student_type_id',
        'level_number'
    ];

    /**
     * Get the student type that this level belongs to.
     */
    public function studentType()
    {
        return $this->belongsTo(StudentType::class);
    }

    /**
     * Get the papers for this level.
     */
    public function papers()
    {
        return $this->hasMany(Paper::class);
    }
}