<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Healthworker extends Model
{
    protected $fillable = [
        'user_id',
        'application_id',
        'name',
        'specialty',
        'resume',
        'license_doc',
        'certifications',
        'application_snapshot_pdf',
        'status',
        'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Define the relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with Application model
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Scope for active healthworkers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for verified healthworkers
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }
}