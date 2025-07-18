<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndividualRequest extends Model
{
    protected $table = 'individual_requests';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
        'care_type',
        'care_requirements',
        'schedule',
        'medical_conditions',
        'medications',
        'emergency_contact',
        'emergency_phone',
        'reference_number',
        'submission_date',
        'csrf_token',
        'qualifications',
        'experience',
        'job_description',
        'job_description_file',
        'status',
        'confirmed',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'confirmed' => 'boolean',
        'status' => 'string',
    ];

    public function getUrgencyAttribute()
    {
        // Assuming urgency is derived from care_requirements or a priority field
        // Since individual_requests doesn't have a priority column, we'll base it on care_requirements
        if (!$this->care_requirements) {
            return 'Medium';
        }

        $urgentKeywords = ['urgent', 'emergency', 'critical'];
        foreach ($urgentKeywords as $keyword) {
            if (stripos($this->care_requirements, $keyword) !== false) {
                return 'High';
            }
        }

        return 'Medium';
    }

    public function getDescriptionAttribute()
    {
        if ($this->care_requirements) {
            return $this->care_requirements;
        }

        $parts = [];
        if ($this->care_type) {
            $parts[] = "Care Type: {$this->care_type}";
        }
        if ($this->schedule) {
            $parts[] = "Schedule: {$this->schedule}";
        }
        if ($this->medical_conditions) {
            $parts[] = "Condition: {$this->medical_conditions}";
        }

        return implode(', ', $parts) ?: 'No description available';
    }
}
