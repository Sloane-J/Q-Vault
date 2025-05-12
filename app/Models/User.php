<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Generate user initials from name
     * 
     * @return string
     */
    public function initials()
    {
        // Split the name into words
        $words = explode(' ', $this->name);
        
        // Take first letter of first and last word (if available)
        $initials = '';
        
        if (!empty($words)) {
            // Always take the first letter of the first word
            $initials .= strtoupper(substr($words[0], 0, 1));
            
            // If there's more than one word, take the first letter of the last word
            if (count($words) > 1) {
                $initials .= strtoupper(substr(end($words), 0, 1));
            }
        }
        
        return $initials ?: 'UN'; // Default to 'UN' if no name is available
    }

    /**
     * Relationship with Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user is an admin
     * 
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a student
     * 
     * @return bool
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }
}