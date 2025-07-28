<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    /**
     * Display facility requests view (returns blade template).
     */
    public function index()
    {
        $facilityRequests = FacilityRequest::latest()->paginate(10);

        $facilityStats = [
            'pending' => FacilityRequest::where('status', 'pending')->count(),
            'approved' => FacilityRequest::where('status', 'approved')->count(),
            'rejected' => FacilityRequest::where('status', 'rejected')->count(),
            'total' => FacilityRequest::count(),
        ];

        return view('admin._facility', [
            'facilityRequests' => $facilityRequests,
            'facilityStats' => $facilityStats,
            'pendingCount' => $facilityStats['pending'],
            'approvedCount' => $facilityStats['approved'],
            'rejectedCount' => $facilityStats['rejected'],
            'totalCount' => $facilityStats['total'],
        ]);
    }

    /**
     * Get facility requests data as JSON for AJAX requests.
     */
    public function getFacilityRequests(): JsonResponse
    {
        $facilityRequests = FacilityRequest::latest()->get()->map(function ($request) {
            return [
                'id' => $request->id,
                'facility_name' => $request->facility_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'location' => $request->coordinates ?? 'N/A',
                'coordinates' => $request->coordinates,
                'staff_needed' => $this->formatJsonField($request->positions) ?? 'Not specified',
                'urgency' => $this->determinePriority($request->priority ?? 'normal'),
                'priority' => $this->determinePriority($request->priority ?? 'normal'),
                'status' => ucfirst($request->status),
                'submitted_at' => $request->created_at->diffForHumans(),
                'description' => $request->job_description ?? 'Not specified',
                'facility_type' => $this->formatJsonField($request->facility_type),
                'employment_type' => $this->formatJsonField($request->employment_type),
                'shift_type' => $this->formatJsonField($request->shift_type),
                'start_date' => $request->start_date ? $request->start_date->toDateString() : 'Not specified',
                'end_date' => $request->end_date ? $request->end_date->toDateString() : null,
                'reference_number' => $request->reference_number,
                'qualifications' => $request->qualifications ?? 'Not specified',
                'experience' => $request->experience ?? 'Not specified',
                'job_description' => $request->job_description ?? 'Not specified',
                'staff_number' => $request->staff_number ?? 1,
                'positions' => $this->formatJsonField($request->positions),
                'other_position' => $request->other_position,
                'other_facility_type' => $request->other_facility_type,
                'job_requirement_option' => $request->job_requirement_option,
            ];
        });

        $stats = [
            'pending' => FacilityRequest::where('status', 'pending')->count(),
            'approved' => FacilityRequest::where('status', 'approved')->count(),
            'rejected' => FacilityRequest::where('status', 'rejected')->count(),
            'total' => FacilityRequest::count(),
        ];

        return response()->json([
            'requests' => $facilityRequests,
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
     * Get a single facility request details.
     */
    public function show(FacilityRequest $facilityRequest): JsonResponse
    {
        return response()->json([
            'id' => $facilityRequest->id,
            'facility_name' => $facilityRequest->facility_name,
            'contact_person' => $facilityRequest->contact_person,
            'email' => $facilityRequest->email,
            'phone' => $facilityRequest->phone,
            'location' => $facilityRequest->coordinates ?? 'N/A',
            'coordinates' => $facilityRequest->coordinates,
            'staff_needed' => $this->formatJsonField($facilityRequest->positions) ?? 'Not specified',
            'urgency' => $this->determinePriority($facilityRequest->priority ?? 'normal'),
            'priority' => $this->determinePriority($facilityRequest->priority ?? 'normal'),
            'status' => ucfirst($facilityRequest->status),
            'submitted_at' => $facilityRequest->created_at->diffForHumans(),
            'description' => $facilityRequest->job_description ?? 'Not specified',
            'facility_type' => $this->formatJsonField($facilityRequest->facility_type),
            'employment_type' => $this->formatJsonField($facilityRequest->employment_type),
            'shift_type' => $this->formatJsonField($facilityRequest->shift_type),
            'start_date' => $facilityRequest->start_date ? $facilityRequest->start_date->toDateString() : 'Not specified',
            'end_date' => $facilityRequest->end_date ? $facilityRequest->end_date->toDateString() : null,
            'reference_number' => $facilityRequest->reference_number,
            'qualifications' => $facilityRequest->qualifications ?? 'Not specified',
            'experience' => $facilityRequest->experience ?? 'Not specified',
            'job_description' => $facilityRequest->job_description ?? 'Not specified',
            'staff_number' => $facilityRequest->staff_number ?? 1,
            'positions' => $this->formatJsonField($facilityRequest->positions),
            'other_position' => $facilityRequest->other_position,
            'other_facility_type' => $facilityRequest->other_facility_type,
            'job_requirement_option' => $facilityRequest->job_requirement_option,
        ]);
    }

    /**
     * Approve a facility request and create task with ONLY fields from the migration.
     */
    public function approve(FacilityRequest $facilityRequest): JsonResponse
    {
        try {
            // Check if migration has been run
            if (!Schema::hasTable('tasks')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tasks table does not exist. Please run the migration first.',
                ], 500);
            }

            // Start a transaction to ensure data integrity
            DB::transaction(function () use ($facilityRequest) {
                // Generate reference number for task if facility request doesn't have one
                $referenceNumber = $facilityRequest->reference_number ?: 'TASK-' . strtoupper(Str::random(8));

                // Prepare required skills array from facility types and positions
                $requiredSkills = [];
                if ($facilityRequest->facility_type) {
                    $facilityTypes = is_string($facilityRequest->facility_type) 
                        ? json_decode($facilityRequest->facility_type, true) 
                        : $facilityRequest->facility_type;
                    if (is_array($facilityTypes)) {
                        $requiredSkills = array_merge($requiredSkills, $facilityTypes);
                    }
                }
                if ($facilityRequest->positions) {
                    $positions = is_string($facilityRequest->positions) 
                        ? json_decode($facilityRequest->positions, true) 
                        : $facilityRequest->positions;
                    if (is_array($positions)) {
                        $requiredSkills = array_merge($requiredSkills, $positions);
                    }
                }

                // Create task using ONLY fields that exist in the migration
                Task::create([
                    // Source tracking
                    'source_type' => 'facility_request',
                    'source_id' => $facilityRequest->id,
                    'reference_number' => $referenceNumber,
                    
                    // Common fields for all task types
                    'title' => $facilityRequest->facility_name,
                    'description' => $facilityRequest->job_description ?: 'Healthcare position at ' . $facilityRequest->facility_name,
                    'coordinates' => $facilityRequest->coordinates,
                    'start_date' => $facilityRequest->start_date,
                    'end_date' => $facilityRequest->end_date,
                    'status' => 'Approved',
                    'priority' => $this->determinePriority($facilityRequest->priority ?? 'Medium'),
                    'confirmed' => false,
                    'complete' => false,
                    
                    // Facility-specific fields (EXACT match to migration)
                    'facility_name' => $facilityRequest->facility_name,
                    'contact_person' => $facilityRequest->contact_person,
                    'email' => $facilityRequest->email,
                    'phone' => $facilityRequest->phone,
                    'facility_type' => $facilityRequest->facility_type,
                    'other_facility_type' => $facilityRequest->other_facility_type,
                    'positions' => $facilityRequest->positions,
                    'other_position' => $facilityRequest->other_position,
                    'employment_type' => $facilityRequest->employment_type,
                    'shift_type' => $facilityRequest->shift_type,
                    'staff_number' => $facilityRequest->staff_number ?? 1,
                    'job_requirement_option' => $facilityRequest->job_requirement_option,
                    'qualifications' => $facilityRequest->qualifications,
                    'experience' => $facilityRequest->experience,
                    'job_description' => $facilityRequest->job_description,
                    
                    // Document storage (if available)
                    'job_description_base64' => $facilityRequest->job_description_base64 ?? null,
                    'job_description_name' => $facilityRequest->job_description_name ?? null,
                    'job_description_mime' => $facilityRequest->job_description_mime ?? null,
                    'job_description_size' => $facilityRequest->job_description_size ?? null,
                    'job_description_file' => $facilityRequest->job_description_file ?? null,
                    
                    // Assignment and tracking
                    'assigned_to' => null,
                    'assigned_to_name' => null,
                    'assigned_at' => null,
                    'started_at' => null,
                    'completed_at' => null,
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                    
                    // System fields
                    'csrf_token' => $facilityRequest->csrf_token ?? null,
                    'ip_address' => $facilityRequest->ip_address ?? null,
                    'user_agent' => $facilityRequest->user_agent ?? null,
                    'form_metadata' => $facilityRequest->form_metadata ?? null,
                    'rejection_reason' => null,
                    'submission_date' => $facilityRequest->submission_date ?? $facilityRequest->created_at,
                    
                    // Legacy fields that might be referenced
                    'required_skills' => $requiredSkills,
                    'staff_needed' => $facilityRequest->staff_number ?? 1,
                    'location' => $facilityRequest->coordinates,
                    'contact_name' => $facilityRequest->contact_person,
                    'contact_email' => $facilityRequest->email,
                    'contact_phone' => $facilityRequest->phone,
                    'rating' => null,
                    'feedback' => null,
                    'urgency' => $this->determinePriority($facilityRequest->priority ?? 'Medium'),
                ]);

                // Update the facility request status
                $facilityRequest->update([
                    'status' => 'approved',
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Facility request approved and converted to task successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a facility request.
     */
    public function reject(FacilityRequest $facilityRequest): JsonResponse
    {
        try {
            $facilityRequest->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'rejection_reason' => request()->input('reason', 'Request does not meet requirements'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facility request rejected successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting request: ' . $e->getMessage(),
            ], 500);
        }
    }
}