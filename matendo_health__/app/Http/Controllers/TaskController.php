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
            'completed' => Task::where('status', 'completed')->count(), // Add this line
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
    $tasks = Task::with('assignedHealthworker')->latest()->get()->map(function ($task) {
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
    // ... your existing fields
                'assigned_to' => $task->assignedHealthworker ? $task->assignedHealthworker->name : null,
                'assigned_at' => $task->assigned_at ? $task->assigned_at->format('M d, Y H:i') : null,
                'complete' => $task->complete, // Add this line

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
        $task->load('assignedHealthworker');
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

       'assigned_to' => $task->assignedHealthworker ? $task->assignedHealthworker->name : 'Unassigned',
        'assigned_at' => $task->assigned_at ? $task->assigned_at->format('M d, Y H:i') : null,

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

public function assign(Request $request, Task $task): JsonResponse
{
    $request->validate([
        'healthworker_id' => 'required|exists:users,id'
    ]);

    // Verify the user is actually a healthworker
    $healthworker = User::where('id', $request->healthworker_id)
                       ->where('usertype', 'healthworker')
                       ->first();

    if (!$healthworker) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid health worker selected'
        ], 400);
    }

    // Update the task with the assigned healthworker
    $task->update([
        'assigned_to' => $request->healthworker_id,
        'assigned_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => "Task assigned to {$healthworker->name} successfully"
    ]);
}


public function complete(Task $task): JsonResponse
{
    try {
        // Verify the task is assigned to the authenticated user
        if (!auth()->check() || auth()->user()->id !== $task->assigned_to) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to complete this task'
            ], 403);
        }

        $task->update(['complete' => true]);
        return response()->json(['success' => true, 'message' => 'Task marked as completed']);
    } catch (\Exception $e) {
        \Log::error('Error marking task as completed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while marking the task as completed.'
        ], 500);
    }
}

// In your controller method that fetches tasks for the dashboard
public function dashboard()
{
    $assignedTasks = Task::where('assigned_to', auth()->id())
                        ->where('status', '!=', 'completed') // Exclude completed tasks
                        ->orderBy('created_at', 'desc')
                        ->get();

    return view('dashboard', compact('assignedTasks'));
}

public function allTasks()
{
    $userId = auth()->id();
    $tasks = Task::where('assigned_to', $userId)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'facility_name', 'status', 'assigned_at', 'completed_at']);

    return response()->json([
        'tasks' => $tasks
    ]);
}

// public function completeTask(Request $request, $taskId)
// {
//     try {
//         // Find the task and ensure it belongs to the authenticated user
//         $task = Task::where('id', $taskId)
//                    ->where('assigned_to', auth()->id())
//                    ->firstOrFail();

//         // Validate the request
//         $request->validate([
//             'completion_notes' => 'required|string'
//         ]);

//         // Update the task status to completed
//         $task->update([
//             'status' => 'completed',
//             'completed_at' => now(),
//             'completion_notes' => $request->completion_notes
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Task marked as completed successfully!'
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Task not found or you do not have permission to complete this task.'
//         ], 404);
//     }
// }

public function completeTask(Request $request, $taskId)
{
    try {
        // Find the task and ensure it belongs to the authenticated user
        $task = Task::where('id', $taskId)
                   ->where('assigned_to', auth()->id())
                   ->firstOrFail();

        // Validate the request
        $request->validate([
            'completion_notes' => 'required|string'
        ]);

        // Update the task status to completed
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $request->completion_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task marked as completed successfully!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Task not found or you do not have permission to complete this task.'
        ], 404);
    }
}
}
