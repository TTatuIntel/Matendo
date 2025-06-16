<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::latest()->paginate(10);

        $stats = [
            'pending' => Task::where('status', 'pending')->count(),
            'approved' => Task::where('status', 'approved')->count(),
            'rejected' => Task::where('status', 'rejected')->count(),
            'total' => Task::count(),
        ];

        return view('admin._tasks', [
            'tasks' => $tasks,
            'pendingCount' => $stats['pending'],
            'approvedCount' => $stats['approved'],
            'rejectedCount' => $stats['rejected'],
            'totalCount' => $stats['total'],
        ]);
    }

    public function getTasks(): JsonResponse
    {
        $tasks = Task::latest()->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'facility_name' => $task->facility_name,
                'phone' => $task->phone,
                'coordinates' => $task->coordinates ?? 'N/A',
                'facility_type' => $task->facility_type,
                'shift_type' => $task->shift_type ?? 'N/A',
                'urgency' => $task->priority ?? 'Medium', // Use priority as urgency
                'status' => ucfirst($task->status),
                'submitted_at' => $task->submission_date ? $task->submission_date->diffForHumans() : 'N/A',
                'job_description' => $task->job_description ?? 'Not specified',
                'qualifications' => $task->qualifications ?? 'Not specified',
                'experience' => $task->experience ?? 'Not specified',
                'medical_conditions' => $task->medical_conditions ?? 'N/A',
                'medications' => $task->medications ?? 'N/A',
                'emergency_contact' => $task->emergency_contact ?? 'N/A',
                'emergency_phone' => $task->emergency_phone ?? 'N/A',
                'reference_number' => $task->reference_number,
            ];
        });

        $stats = [
            'pending' => Task::where('status', 'pending')->count(),
            'approved' => Task::where('status', 'approved')->count(),
            'rejected' => Task::where('status', 'rejected')->count(),
            'total' => Task::count(),
        ];

        return response()->json([
            'tasks' => $tasks,
            'stats' => $stats,
        ]);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'id' => $task->id,
            'facility_name' => $task->facility_name,
            'contact_person' => $task->contact_person,
            'email' => $task->email,
            'phone' => $task->phone,
            'coordinates' => $task->coordinates ?? 'N/A',
            'facility_type' => $task->facility_type,
            'positions' => $task->positions,
            'employment_type' => $task->employment_type,
            'shift_type' => $task->shift_type,
            'staff_number' => $task->staff_number,
            'start_date' => $task->start_date,
            'medical_conditions' => $task->medical_conditions ?? 'N/A',
            'medications' => $task->medications ?? 'N/A',
            'emergency_contact' => $task->emergency_contact ?? 'N/A',
            'emergency_phone' => $task->emergency_phone ?? 'N/A',
            'reference_number' => $task->reference_number,
            'urgency' => $task->priority ?? 'Medium',
            'status' => ucfirst($task->status),
            'submitted_at' => $task->submission_date ? $task->submission_date->diffForHumans() : 'N/A',
            'job_description' => $task->job_description ?? 'Not specified',
            'qualifications' => $task->qualifications ?? 'Not specified',
            'experience' => $task->experience ?? 'Not specified',
        ]);
    }
}
