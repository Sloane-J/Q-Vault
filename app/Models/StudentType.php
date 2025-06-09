<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentType extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_type';
    
    protected $fillable = [
        'name',
        'description',
    ];
    
    /**
     * Get the levels for this student type.
     */
    public function levels()
    {
        return $this->hasMany(Level::class);
    }
    
    public static function getTypes(): array
    {
        return [
            'hnd' => 'HND',
            'btech' => 'B-Tech',
            'topup' => 'Top-Up',
            'dbs' => 'DBS',
            'mtech' => 'MTech',
        ];
    }
    
    public static function getLevels($type): array
    {
        return match ($type) {
            'hnd' => [100, 200, 300],
            'btech' => [100, 200, 300, 400],
            'topup' => [300, 400],
            'dbs' => [100, 200],	
            'mtech' => [500, 600],
            
            default => [],
        };
    }
}