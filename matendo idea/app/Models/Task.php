<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'facility_name',
        'contact_person',
        'email',
        'phone',
        'coordinates',
        'facility_type',
        'positions',
        'employment_type',
        'shift_type',
        'staff_number',
        'start_date',
        'job_requirement_option',
        'qualifications',
        'experience',
        'job_description',
        'job_description_file',
        'reference_number',
        'status',
        'priority',
        'confirmed',
        'csrf_token',
        'submission_date',
        'emergency_contact', // Added
        'emergency_phone',  // Added
        'medical_conditions', // Added
        'medications',      // Added
    ];

    protected $casts = [
        'start_date' => 'date',
        'submission_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'confirmed' => 'boolean',
        'status' => 'string',
    ];

    public function getStaffNeededAttribute()
    {
        $positions = $this->positions;
        $staffNumber = $this->staff_number;

        if ($positions && $staffNumber) {
            return "{$positions} ({$staffNumber})";
        } elseif ($positions) {
            return $positions;
        } elseif ($staffNumber) {
            return "Staff needed: {$staffNumber}";
        }

        return 'N/A';
    }

    public function getUrgencyAttribute()
    {
        if (!$this->priority) {
            return 'Medium';
        }

        switch (strtolower($this->priority)) {
            case 'high':
            case 'urgent':
                return 'High';
            case 'low':
                return 'Low';
            default:
                return 'Medium';
        }
    }

    public function getDescriptionAttribute()
    {
        if ($this->job_description) {
            return $this->job_description;
        }

        $parts = [];
        if ($this->positions) {
            $parts[] = "Position: {$this->positions}";
        }
        if ($this->employment_type) {
            $parts[] = "Employment: {$this->employment_type}";
        }
        if ($this->shift_type) {
            $parts[] = "Shift: {$this->shift_type}";
        }
        if ($this->care_type) {
            $parts[] = "Care Type: {$this->care_type}";
        }

        return implode(', ', $parts) ?: 'No description available';
    }
}
