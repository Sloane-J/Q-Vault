<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Good practice to include
use Illuminate\Database\Eloquent\Relations\MorphTo; // For polymorphic relationships

class AuditLog extends Model
{
    use HasFactory; // You might need to run `php artisan make:factory AuditLogFactory` for this

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activity_log'; // Explicitly set to your table name

    /**
     * The attributes that are mass assignable.
     *
     * These typically correspond to the columns in your 'activity_log' table
     * that you might set when creating a new log entry.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'log_name',
        'description',
        'subject_type', 
        'subject_id',   
        'causer_type',  
        'causer_id',    
        'properties',
        'batch_uuid' 
    ];

    /**
     * The attributes that should be cast.
     *
     * The 'properties' column is stored as JSON in your database.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array', // Casts the JSON 'properties' column to a PHP array
    ];


    /**
     * Get the subject of the activity.
     * This is the model that the activity happened on (e.g., a Paper, a User).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity.
     * This is typically the user who performed the activity.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }


public function user()
{
   return $this->belongsTo(User::class, 'causer_id');

}

}