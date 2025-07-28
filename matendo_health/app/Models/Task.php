<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        // Source tracking
        'source_type',
        'source_id',
        'reference_number',
        
        // Common fields for all task types
        'title',
        'description',
        'coordinates',
        'start_date',
        'end_date',
        'status',
        'priority',
        'confirmed',
        'complete',
        
        // Facility-specific fields
        'facility_name',
        'contact_person',
        'email',
        'phone',
        'facility_type',
        'other_facility_type',
        'positions',
        'other_position',
        'employment_type',
        'shift_type',
        'staff_number',
        'job_requirement_option',
        'qualifications',
        'experience',
        'job_description',
        
        // Individual-specific fields
        'full_name',
        'address',
        'care_type',
        'care_requirements',
        'schedule',
        'medical_conditions',
        'medications',
        'emergency_contact',
        'emergency_phone',
        
        // Document storage
        'job_description_base64',
        'job_description_name',
        'job_description_mime',
        'job_description_size',
        'job_description_file',
        
        // Assignment and tracking
        'assigned_to',
        'assigned_to_name',
        'assigned_at',
        'started_at',
        'completed_at',
        'processed_at',
        'processed_by',
        
        // System fields
        'csrf_token',
        'ip_address',
        'user_agent',
        'form_metadata',
        'rejection_reason',
        'submission_date',
        
        // Legacy fields
        'required_skills',
        'staff_needed',
        'location',
        'contact_name',
        'contact_email',
        'contact_phone',
        'rating',
        'feedback',
        'urgency',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'processed_at' => 'datetime',
        'submission_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'confirmed' => 'boolean',
        'complete' => 'boolean',
        'required_skills' => 'array',
        'facility_type' => 'array',
        'positions' => 'array',
        'employment_type' => 'array',
        'shift_type' => 'array',
        'schedule' => 'array',
    ];

    // Relationship to get assigned healthworker
    public function assignedHealthworker()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relationship to get processor
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Accessors for backward compatibility with facility request fields
    public function getFacilityNameAttribute($value)
    {
        return $value ?: $this->title;
    }

    public function getContactPersonAttribute($value)
    {
        return $value ?: $this->contact_name;
    }

    public function getEmailAttribute($value)
    {
        return $value ?: $this->contact_email;
    }

    public function getPhoneAttribute($value)
    {
        return $value ?: $this->contact_phone;
    }

    public function getFacilityTypeAttribute($value)
    {
        if ($value) {
            return is_array($value) ? $value : json_decode($value, true);
        }
        return $this->required_skills ? (is_array($this->required_skills) ? $this->required_skills : [$this->required_skills]) : null;
    }

    public function getPositionsAttribute($value)
    {
        if ($value) {
            return is_array($value) ? $value : json_decode($value, true);
        }
        return $this->description ? [$this->description] : null;
    }

    public function getEmploymentTypeAttribute($value)
    {
        if ($value) {
            return is_array($value) ? $value : json_decode($value, true);
        }
        return ['Full-time']; // Default value
    }

    public function getShiftTypeAttribute($value)
    {
        if ($value) {
            return is_array($value) ? $value : json_decode($value, true);
        }
        return ['Day']; // Default value
    }

    public function getStaffNumberAttribute($value)
    {
        return $value ?: $this->staff_needed ?: 1;
    }

    public function getJobDescriptionAttribute($value)
    {
        return $value ?: $this->description;
    }

    public function getQualificationsAttribute($value)
    {
        return $value ?: ($this->required_skills ? (is_array($this->required_skills) ? implode(', ', $this->required_skills) : $this->required_skills) : null);
    }

    public function getExperienceAttribute($value)
    {
        return $value ?: 'As required'; // Default value
    }

    public function getMedicalConditionsAttribute($value)
    {
        return $value;
    }

    public function getMedicationsAttribute($value)
    {
        return $value;
    }

    public function getEmergencyContactAttribute($value)
    {
        return $value;
    }

    public function getEmergencyPhoneAttribute($value)
    {
        return $value;
    }

    public function getFullNameAttribute($value)
    {
        return $value ?: $this->contact_name;
    }

    public function getAddressAttribute($value)
    {
        return $value ?: $this->location;
    }

    public function getCareTypeAttribute($value)
    {
        return $value ?: ($this->required_skills ? (is_array($this->required_skills) ? implode(', ', $this->required_skills) : $this->required_skills) : null);
    }

    public function getAssignedToNameAttribute($value)
    {
        return $value ?: ($this->assignedHealthworker ? $this->assignedHealthworker->name : null);
    }

    // Legacy accessors
    public function getStaffNeededAttribute($value)
    {
        return $value ?: $this->staff_number ?: 1;
    }

    public function getUrgencyAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
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

    public function getLocationAttribute($value)
    {
        return $value ?: $this->coordinates;
    }

    public function getContactNameAttribute($value)
    {
        return $value ?: $this->contact_person;
    }

    public function getContactEmailAttribute($value)
    {
        return $value ?: $this->email;
    }

    public function getContactPhoneAttribute($value)
    {
        return $value ?: $this->phone;
    }

    // Scope for filtering
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'In Progress');
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'Completed' || $this->complete;
    }

    public function isAssigned()
    {
        return !empty($this->assigned_to);
    }

    public function isPending()
    {
        return $this->status === 'Pending';
    }

    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    public function isRejected()
    {
        return $this->status === 'Rejected';
    }

    public function isInProgress()
    {
        return $this->status === 'In Progress';
    }

    // Format methods for display
    public function getFormattedFacilityType()
    {
        $facilityType = $this->facility_type;
        if (is_array($facilityType)) {
            return implode(', ', $facilityType);
        }
        return $facilityType ?: 'Not specified';
    }

    public function getFormattedPositions()
    {
        $positions = $this->positions;
        if (is_array($positions)) {
            return implode(', ', $positions);
        }
        return $positions ?: 'Not specified';
    }

    public function getFormattedEmploymentType()
    {
        $employmentType = $this->employment_type;
        if (is_array($employmentType)) {
            return implode(', ', $employmentType);
        }
        return $employmentType ?: 'Not specified';
    }

    public function getFormattedShiftType()
    {
        $shiftType = $this->shift_type;
        if (is_array($shiftType)) {
            return implode(', ', $shiftType);
        }
        return $shiftType ?: 'Not specified';
    }

    public function getFormattedRequiredSkills()
    {
        $requiredSkills = $this->required_skills;
        if (is_array($requiredSkills)) {
            return implode(', ', $requiredSkills);
        }
        return $requiredSkills ?: 'Not specified';
    }
}