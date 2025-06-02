<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndividualRequest extends Model
{
    protected $fillable = [
        'reference_number',
        'individual_name',
        'contact_person',
        'email',
        'phone',
        'location',
        'coordinates',
        'request_types',
        'other_request_type',
        'skills_needed',
        'other_skill',
        'employment_types',
        'shift_types',
        'staff_number',
        'start_date',
        'requirement_option',
        'qualifications',
        'experience',
        'job_description',
        'job_description_file',
        'status',
        'confirmed',
    ];

    protected $casts = [
        'confirmed' => 'boolean',
        'start_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function assignment()
    {
        return $this->hasOne(TaskAssignment::class, 'task_id')->where('task_type', 'individual_request');
    }

    public function isAssigned()
    {
        return $this->assignment()->exists();
    }
}
