<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    protected $fillable = ['task_type', 'task_id', 'assigned_to'];

   // Relationship with User (worker)
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relationship with FacilityRequest
    public function facilityRequest()
    {
        return $this->belongsTo(FacilityRequest::class, 'task_id');
    }

    // Relationship with IndividualRequest
    public function individualRequest()
    {
        return $this->belongsTo(IndividualRequest::class, 'task_id');
    }
}
