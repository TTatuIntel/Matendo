<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_type',
        'task_id',
        'assignee_id',
        'assigned_by',
        'assigned_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    // Relationship to the assignee (assuming it's from applications table)
    public function assignee()
    {
        return $this->belongsTo(\App\Models\Application::class, 'assignee_id');
    }

    // Relationship to who assigned the task
    public function assigner()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }

    // Polymorphic relationship to get the actual task
    public function task()
    {
        if ($this->task_type === 'facility_booking') {
            return $this->belongsTo(\App\Models\FacilityRequest::class, 'task_id');
        } elseif ($this->task_type === 'individual_request') {
            return $this->belongsTo(\App\Models\IndividualRequest::class, 'task_id');
        }
        return null;
    }
}
