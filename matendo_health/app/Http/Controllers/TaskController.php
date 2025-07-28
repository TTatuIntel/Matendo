<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display tasks view (returns blade template).
     */
    public function index()
    {
        $tasks = Task::with('assignedHealthworker')->latest()->paginate(10);

        $taskStats = [
            'pending' => Task::where('status', 'Pending')->count(),
            'approved' => Task::where('status', 'Approved')->count(),
            'rejected' => Task::where('status', 'Rejected')->count(),
            'completed' => Task::where('status', 'Completed')->count(),
            'total' => Task::count(),
        ];

        return view('admin._tasks', [
            'tasks' => $tasks,
            'taskStats' => $taskStats,
            'pendingCount' => $taskStats['pending'],
            'approvedCount' => $taskStats['approved'],
            'rejectedCount' => $taskStats['rejected'],
            'completedCount' => $taskStats['completed'],
            'totalCount' => $taskStats['total'],
        ]);
    }

    /**
     * Get tasks data as JSON for AJAX requests.
     */
    public function getTasks(): JsonResponse
    {
        $tasks = Task::with('assignedHealthworker')->latest()->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'facility_name' => $task->facility_name ?: $task->title,
                'full_name' => $task->full_name,
                'title' => $task->title,
                'contact_person' => $task->contact_person ?: $task->contact_name,
                'email' => $task->email ?: $task->contact_email,
                'phone' => $task->phone ?: $task->contact_phone,
                'coordinates' => $task->coordinates ?: $task->location,
                'location' => $task->location ?: $task->coordinates,
                'staff_needed' => $task->staff_number ?: $task->staff_needed ?: 1,
                'staff_number' => $task->staff_number ?: $task->staff_needed ?: 1,
                'urgency' => $this->determinePriority($task->priority ?: 'Medium'),
                'priority' => $task->priority ?: 'Medium',
                'status' => $task->status ?: 'Pending',
                'submitted_at' => $task->created_at->diffForHumans(),
                'description' => $task->description,
                'job_description' => $task->job_description ?: $task->description,
                'facility_type' => $this->formatJsonField($task->facility_type),
                'care_type' => $task->care_type,
                'positions' => $this->formatJsonField($task->positions),
                'employment_type' => $this->formatJsonField($task->employment_type),
                'shift_type' => $this->formatJsonField($task->shift_type),
                'start_date' => $task->start_date ? $task->start_date->toDateString() : null,
                'end_date' => $task->end_date ? $task->end_date->toDateString() : null,
                'reference_number' => $task->reference_number,
                'qualifications' => $task->qualifications,
                'experience' => $task->experience,
                'assigned_to' => $task->assigned_to,
                'assigned_to_name' => $task->assigned_to_name ?: ($task->assignedHealthworker ? $task->assignedHealthworker->name : null),
                'assigned_at' => $task->assigned_at ? $task->assigned_at->diffForHumans() : null,
                'completed_at' => $task->completed_at ? $task->completed_at->diffForHumans() : null,
                'medical_conditions' => $task->medical_conditions,
                'medications' => $task->medications,
                'emergency_contact' => $task->emergency_contact,
                'emergency_phone' => $task->emergency_phone,
                'care_requirements' => $task->care_requirements,
                'address' => $task->address ?: $task->location,
                'complete' => $task->complete,
                'confirmed' => $task->confirmed,
            ];
        });

        $stats = [
            'pending' => Task::where('status', 'Pending')->count(),
            'approved' => Task::where('status', 'Approved')->count(),
            'rejected' => Task::where('status', 'Rejected')->count(),
            'completed' => Task::where('status', 'Completed')->count(),
            'total' => Task::count(),
        ];

        return response()->json([
            'tasks' => $tasks,
            'stats' => $stats,
        ]);
    }

    /**
     * Format JSON field for display
     */
    private function formatJsonField($jsonField)
    {
        if (empty($jsonField)) {
            return 'Not specified';
        }

        if (is_string($jsonField)) {
            $decoded = json_decode($jsonField, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return implode(', ', $decoded);
            }
            return $jsonField;
        }

        if (is_array($jsonField)) {
            return implode(', ', $jsonField);
        }

        return 'Not specified';
    }

    /**
     * Determine priority level for display
     */
    private function determinePriority($priority)
    {
        switch (strtolower($priority)) {
            case 'urgent':
                return 'Urgent';
            case 'high':
                return 'High';
            case 'low':
                return 'Low';
            default:
                return 'Medium';
        }
    }

    /**
     * Get a single task details.
     */
    public function show(Task $task): JsonResponse
    {
        $task->load('assignedHealthworker');

        return response()->json([
            'id' => $task->id,
            'facility_name' => $task->facility_name ?: $task->title,
            'full_name' => $task->full_name,
            'title' => $task->title,
            'contact_person' => $task->contact_person ?: $task->contact_name,
            'email' => $task->email ?: $task->contact_email,
            'phone' => $task->phone ?: $task->contact_phone,
            'coordinates' => $task->coordinates ?: $task->location,
            'location' => $task->location ?: $task->coordinates,
            'staff_needed' => $task->staff_number ?: $task->staff_needed ?: 1,
            'staff_number' => $task->staff_number ?: $task->staff_needed ?: 1,
            'urgency' => $this->determinePriority($task->priority ?: 'Medium'),
            'priority' => $task->priority ?: 'Medium',
            'status' => $task->status ?: 'Pending',
            'submitted_at' => $task->created_at->diffForHumans(),
            'description' => $task->description,
            'job_description' => $task->job_description ?: $task->description,
            'facility_type' => $this->formatJsonField($task->facility_type),
            'care_type' => $task->care_type,
            'positions' => $this->formatJsonField($task->positions),
            'employment_type' => $this->formatJsonField($task->employment_type),
            'shift_type' => $this->formatJsonField($task->shift_type),
            'start_date' => $task->start_date ? $task->start_date->toDateString() : null,
            'end_date' => $task->end_date ? $task->end_date->toDateString() : null,
            'reference_number' => $task->reference_number,
            'qualifications' => $task->qualifications,
            'experience' => $task->experience,
            'assigned_to' => $task->assigned_to,
            'assigned_to_name' => $task->assigned_to_name ?: ($task->assignedHealthworker ? $task->assignedHealthworker->name : null),
            'assigned_at' => $task->assigned_at ? $task->assigned_at->diffForHumans() : null,
            'completed_at' => $task->completed_at ? $task->completed_at->diffForHumans() : null,
            'medical_conditions' => $task->medical_conditions,
            'medications' => $task->medications,
            'emergency_contact' => $task->emergency_contact,
            'emergency_phone' => $task->emergency_phone,
            'care_requirements' => $task->care_requirements,
            'address' => $task->address ?: $task->location,
            'complete' => $task->complete,
            'confirmed' => $task->confirmed,
        ]);
    }

    /**
     * Store a new task.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'facility_name' => 'nullable|string',
                'full_name' => 'nullable|string',
                'contact_person' => 'nullable|string',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'facility_type' => 'nullable|string',
                'care_type' => 'nullable|string',
                'positions' => 'nullable|string',
                'employment_type' => 'nullable|string',
                'shift_type' => 'nullable|string',
                'staff_number' => 'nullable|integer|min:1',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'priority' => 'nullable|in:Low,Medium,High,Urgent',
                'job_description' => 'nullable|string',
                'qualifications' => 'nullable|string',
                'experience' => 'nullable|string',
                'location' => 'nullable|string',
                'coordinates' => 'nullable|string',
                'address' => 'nullable|string',
                'care_requirements' => 'nullable|string',
                'medical_conditions' => 'nullable|string',
                'medications' => 'nullable|string',
                'emergency_contact' => 'nullable|string',
                'emergency_phone' => 'nullable|string',
            ]);

            // Generate reference number
            $referenceNumber = 'TASK-' . strtoupper(Str::random(8));

            // Prepare required skills
            $requiredSkills = [];
            if (!empty($data['facility_type'])) {
                $requiredSkills[] = $data['facility_type'];
            }
            if (!empty($data['care_type'])) {
                $requiredSkills[] = $data['care_type'];
            }
            if (!empty($data['positions'])) {
                $requiredSkills[] = $data['positions'];
            }

            $task = Task::create([
                'source_type' => 'manual',
                'reference_number' => $referenceNumber,
                'title' => $data['facility_name'] ?: $data['full_name'] ?: 'New Task',
                'description' => $data['job_description'] ?: 'Task created manually',
                'location' => $data['location'] ?: $data['coordinates'] ?: $data['address'],
                'coordinates' => $data['coordinates'] ?: $data['location'],
                'required_skills' => $requiredSkills,
                'staff_needed' => $data['staff_number'] ?: 1,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => 'Approved', // Auto-approve manually created tasks
                'priority' => $data['priority'] ?: 'Medium',
                'contact_name' => $data['contact_person'],
                'contact_email' => $data['email'],
                'contact_phone' => $data['phone'],
                
                // Facility fields
                'facility_name' => $data['facility_name'],
                'facility_type' => $data['facility_type'] ? [$data['facility_type']] : null,
                'positions' => $data['positions'] ? [$data['positions']] : null,
                'employment_type' => $data['employment_type'] ? [$data['employment_type']] : null,
                'shift_type' => $data['shift_type'] ? [$data['shift_type']] : null,
                'staff_number' => $data['staff_number'] ?: 1,
                'job_description' => $data['job_description'],
                'qualifications' => $data['qualifications'],
                'experience' => $data['experience'],
                
                // Individual fields
                'full_name' => $data['full_name'],
                'address' => $data['address'] ?: $data['location'],
                'care_type' => $data['care_type'],
                'care_requirements' => $data['care_requirements'],
                'medical_conditions' => $data['medical_conditions'],
                'medications' => $data['medications'],
                'emergency_contact' => $data['emergency_contact'],
                'emergency_phone' => $data['emergency_phone'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating task: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve a task.
     */
    public function approve(Task $task): JsonResponse
    {
        try {
            $task->update([
                'status' => 'Approved',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task approved successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving task: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a task.
     */
    public function reject(Task $task): JsonResponse
    {
        try {
            $task->update([
                'status' => 'Rejected',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'rejection_reason' => request()->input('reason', 'Task does not meet requirements'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task rejected successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting task: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign a task to a healthworker.
     */
    public function assign(Task $task): JsonResponse
    {
        try {
            $request = request();
            $healthworkerId = $request->input('healthworker_id');

            if (!$healthworkerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a health worker.',
                ], 400);
            }

            $healthworker = User::find($healthworkerId);
            if (!$healthworker) {
                return response()->json([
                    'success' => false,
                    'message' => 'Health worker not found.',
                ], 404);
            }

            $task->update([
                'assigned_to' => $healthworkerId,
                'assigned_to_name' => $healthworker->name,
                'assigned_at' => now(),
                'status' => 'In Progress',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task assigned successfully to ' . $healthworker->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning task: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete a task.
     */
    public function complete(Task $task): JsonResponse
    {
        try {
            $task->update([
                'status' => 'Completed',
                'completed_at' => now(),
                'complete' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task marked as completed!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing task: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete task by ID (alternative method for different route patterns).
     */
    public function completeTask($taskId): JsonResponse
    {
        try {
            $task = Task::findOrFail($taskId);
            return $this->complete($task);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing task: ' . $e->getMessage(),
            ], 500);
        }
    }
}