<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Note: We don't have a student_types table in migrations,
    // This is more of a helper model for validation/selection
    
    public static function getTypes(): array
    {
        return [
            'hnd' => 'HND',
            'btech' => 'B-Tech',
            'topup' => 'Top-Up'
        ];
    }
    
    public static function getLevels($type): array
    {
        return match ($type) {
            'hnd' => [100, 200, 300],
            'btech' => [100, 200, 300, 400],
            'topup' => [300, 400],
            default => [],
        };
    }
}