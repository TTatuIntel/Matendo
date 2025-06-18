<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'location',
        'coordinates',
        'profession',
        'other_profession',
        'specialization',
        'years_experience',
        'license_number',
        'resume',
        'license_doc',
        'certifications',
        'work_type',
        'shift_type',
        'preferred_location',
        'start_date',
        'status',
        'confirmed',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_date' => 'date',
        'confirmed' => 'boolean',
        'years_experience' => 'integer',
    ];

    // Scope for pending applications
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for approved applications
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope for rejected applications
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Get full name attribute
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Get formatted created date
    public function getFormattedCreatedDateAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    // Check if application is pending
    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Check if application is approved
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    // Check if application is rejected
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Relationship with HealthWorker
    public function healthWorker()
    {
        return $this->hasOne(HealthWorker::class);
    }
}