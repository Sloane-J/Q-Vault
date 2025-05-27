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

    /**
     * Get all exam types for dropdowns/selects
     */
    public static function getAllTypes()
    {
        return self::orderBy('name')->get();
    }

    /**
     * Get exam type options as key-value pairs
     */
    public static function getTypeOptions()
    {
        return self::pluck('name', 'id')->toArray();
    }
}