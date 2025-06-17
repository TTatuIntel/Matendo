<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $healthworkers = User::where('usertype', 'healthworker')->select('id', 'name')->get();

        return view('tasks', [
            'tasks' => $tasks,
            'pendingCount' => $stats['pending'],
            'approvedCount' => $stats['approved'],
            'rejectedCount' => $stats['rejected'],
            'totalCount' => $stats['total'],
            'healthworkers' => $healthworkers,
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
                'urgency' => $task->priority ?? 'Medium',
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
                'positions' => $task->positions,
                'staff_number' => $task->staff_number,
                'start_date' => $task->start_date ? $task->start_date->format('M d, Y') : 'N/A',
                'contact_person' => $task->contact_person,
                'email' => $task->email,
                'employment_type' => $task->employment_type,
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
            'start_date' => $task->start_date ? $task->start_date->format('M d, Y') : 'N/A',
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'facility_type' => 'required|string|max:255',
            'positions' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'shift_type' => 'required|string|max:255',
            'staff_number' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'priority' => 'nullable|string|in:Low,Medium,High',
            'job_description' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'experience' => 'nullable|string',
        ]);

        $task = Task::create(array_merge($validated, [
            'reference_number' => 'TASK-' . strtoupper(uniqid()),
            'status' => 'pending',
            'submission_date' => now(),
            'csrf_token' => csrf_token(),
        ]));

        return response()->json(['success' => true, 'message' => 'Task created successfully']);
    }

    public function approve(Task $task): JsonResponse
    {
        $task->update(['status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'Task approved successfully']);
    }

    public function reject(Task $task): JsonResponse
    {
        $task->update(['status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'Task rejected successfully']);
    }



}
