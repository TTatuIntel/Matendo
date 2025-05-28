<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityRequest extends Model
{
    protected $table = 'facility_requests';

    protected $fillable = [
        'reference_number',
        'facility_name',
        'contact_person',
        'email',
        'phone',
        'location',
        'coordinates',
        'facility_types',
        'other_facility_type',
        'resources_needed',
        'worker_position',
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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'confirmed' => 'boolean',
    ];
}
