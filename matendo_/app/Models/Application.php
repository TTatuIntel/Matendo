<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Application extends Model
{
    use HasFactory;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_code',
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
   
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'work_type' => 'array',
        'shift_type' => 'array',
        'start_date' => 'date',
        'confirmed' => 'boolean',
    ];
   
    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
   
    /**
     * Get the status badge color
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ][$this->status] ?? 'secondary';
    }
   
    /**
     * Check if an application is still pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
   
    /**
     * Check if an application is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }
   
    /**
     * Check if an application is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}