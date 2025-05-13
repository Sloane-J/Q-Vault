<?php

namespace App\Models;

// Remove: use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- ADDED: For the sessions relationship
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- ADDED: For the department relationship (assuming Department model exists)

class User extends Authenticatable // <-- REMOVED: implements MustVerifyEmail
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
        'department_id', // Assuming you have a departments table and Department model
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
            'email_verified_at' => 'datetime', // Still good to have the column, even if not enforcing verification
            'password' => 'hashed',
        ];
    }

    /**
     * Generate user initials from name
     *
     * @return string
     */
    public function initials(): string
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

        return $initials ?: 'UN'; // Default to 'UN' if no name or only spaces in name
    }

    /**
     * Relationship with Department.
     */
    public function department(): BelongsTo // Added type hint
    {
        // Make sure you have an App\Models\Department model
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a student.
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Get the sessions for the user.
     * ADDED FOR SESSION TRACKING
     */
    public function sessions(): HasMany
    {
        // Assumes your 'sessions' table has a 'user_id' foreign key
        // and your Session model is App\Models\Session (created in the previous step)
        return $this->hasMany(Session::class);
    }

        // In User model
    public function uploadedPapers()
    {
        return $this->hasMany(Paper::class, 'uploaded_by');
    }

    public function uploadedVersions()
    {
        return $this->hasMany(PaperVersion::class, 'uploaded_by');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'head_of_department_id');
    }

    public function downloads()
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function views()
    {
        return $this->hasMany(ViewLog::class);
    }
}