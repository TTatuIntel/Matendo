<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'causer_type',
        'causer_id',
        'properties',
        'batch_uuid'
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeInLog($query, string $logName)
    {
        return $query->where('log_name', $logName);
    }

    public function scopeCausedBy($query, Model $causer)
    {
        return $query->where('causer_type', $causer->getMorphClass())
                    ->where('causer_id', $causer->getKey());
    }

    public function scopeForSubject($query, Model $subject)
    {
        return $query->where('subject_type', $subject->getMorphClass())
                    ->where('subject_id', $subject->getKey());
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    // Helper methods
    public static function logActivity(string $logName, string $description, Model $subject = null, Model $causer = null, array $properties = [])
    {
        return static::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'causer_type' => $causer?->getMorphClass() ?? auth()->user()?->getMorphClass(),
            'causer_id' => $causer?->getKey() ?? auth()->id(),
            'properties' => $properties
        ]);
    }

    public function getChangedAttribute(): array
    {
        return $this->properties['attributes'] ?? [];
    }

    public function getOldAttribute(): array
    {
        return $this->properties['old'] ?? [];
    }
}
