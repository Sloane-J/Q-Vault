<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SystemEvent extends Model
{
    use HasFactory;

    /**
     * Event type constants
     */
    const TYPE_SECURITY = 'security';
    const TYPE_ERROR = 'error';
    const TYPE_UPLOAD = 'upload';
    const TYPE_DOWNLOAD = 'download';
    const TYPE_AUTH = 'authentication';
    const TYPE_ADMIN = 'admin';
    const TYPE_SYSTEM = 'system';

    /**
     * Event severity constants
     */
    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_ERROR = 'error';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_type',
        'description',
        'user_id',
        'ip_address',
        'user_agent', 
        'severity',
        'resource_type',
        'resource_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user associated with this event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include events of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope a query to only include events with a specific severity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $severity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope a query to only include events from a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include events within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include events related to a specific resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $resourceType
     * @param  int|null  $resourceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForResource($query, $resourceType, $resourceId = null)
    {
        if ($resourceId) {
            return $query->where('resource_type', $resourceType)
                        ->where('resource_id', $resourceId);
        }
        
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope a query to only include security events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSecurity($query)
    {
        return $query->where('event_type', self::TYPE_SECURITY);
    }

    /**
     * Scope a query to only include error events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeErrors($query)
    {
        return $query->where('event_type', self::TYPE_ERROR);
    }

    /**
     * Scope a query to only include authentication events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthentication($query)
    {
        return $query->where('event_type', self::TYPE_AUTH);
    }

    /**
     * Scope a query to only include critical events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }

    /**
     * Scope a query to only include events from a specific IP address.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $ipAddress
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Log a security event.
     *
     * @param  string  $description
     * @param  string  $severity
     * @param  array  $metadata
     * @param  int|null  $userId
     * @return SystemEvent
     */
    public static function logSecurityEvent($description, $severity = self::SEVERITY_INFO, $metadata = [], $userId = null)
    {
        return self::createEvent(
            self::TYPE_SECURITY,
            $description,
            $severity,
            null,
            null,
            $metadata,
            $userId
        );
    }

    /**
     * Log an error event.
     *
     * @param  string  $description
     * @param  string  $severity
     * @param  array  $metadata
     * @param  int|null  $userId
     * @return SystemEvent
     */
    public static function logErrorEvent($description, $severity = self::SEVERITY_ERROR, $metadata = [], $userId = null)
    {
        return self::createEvent(
            self::TYPE_ERROR,
            $description,
            $severity,
            null,
            null,
            $metadata,
            $userId
        );
    }

    /**
     * Log an upload event.
     *
     * @param  string  $description
     * @param  string  $resourceType
     * @param  int  $resourceId
     * @param  array  $metadata
     * @param  int|null  $userId
     * @return SystemEvent
     */
    public static function logUploadEvent($description, $resourceType, $resourceId, $metadata = [], $userId = null)
    {
        return self::createEvent(
            self::TYPE_UPLOAD,
            $description,
            self::SEVERITY_INFO,
            $resourceType,
            $resourceId,
            $metadata,
            $userId
        );
    }

    /**
     * Log a download event.
     *
     * @param  string  $description
     * @param  string  $resourceType
     * @param  int  $resourceId
     * @param  array  $metadata
     * @param  int|null  $userId
     * @return SystemEvent
     */
    public static function logDownloadEvent($description, $resourceType, $resourceId, $metadata = [], $userId = null)
    {
        return self::createEvent(
            self::TYPE_DOWNLOAD,
            $description,
            self::SEVERITY_INFO,
            $resourceType,
            $resourceId,
            $metadata,
            $userId
        );
    }

    /**
     * Log an authentication event.
     *
     * @param  string  $description
     * @param  string  $severity
     * @param  array  $metadata
     * @param  int|null  $userId
     * @return SystemEvent
     */
    public static function logAuthEvent($description, $severity = self::SEVERITY_INFO, $metadata = [], $userId = null)
    {
        return self::createEvent(
            self::TYPE_AUTH,
            $description,
            $severity,
            null,
            null,
            $metadata,
            $userId
        );
    }

    /**
     * Create a new system event.
     *
     * @param  string  $eventType
     * @param  string  $description
     * @param  string  $severity
     * @param  string|null  $resourceType
     * @param  int|null  $resourceId
     * @param  array  $metadata
     * @param  int|null  $userId
     * @return SystemEvent
     */
    protected static function createEvent(
        $eventType,
        $description,
        $severity = self::SEVERITY_INFO,
        $resourceType = null,
        $resourceId = null,
        $metadata = [],
        $userId = null
    ) {
        // Try to get the current user if not specified
        if ($userId === null && auth()->check()) {
            $userId = auth()->id();
        }

        // Get the IP address and user agent
        $request = request();
        $ipAddress = $request ? $request->ip() : null;
        $userAgent = $request ? $request->userAgent() : null;

        return self::create([
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'severity' => $severity,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'metadata' => $metadata,
        ]);
    }
}