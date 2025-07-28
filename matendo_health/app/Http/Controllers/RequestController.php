<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
                'staff_needed' => $this->formatJsonField($request->positions) ?? 'Not specified',
                'urgency' => $this->determinePriority($request->priority ?? 'normal'),
                'status' => ucfirst($request->status),
                'submitted_at' => $request->created_at->diffForHumans(),
                'description' => $request->job_description ?? 'Not specified',
                'facility_type' => $this->formatJsonField($request->facility_type),
                'employment_type' => $this->formatJsonField($request->employment_type),
                'shift_type' => $this->formatJsonField($request->shift_type),
                'start_date' => $request->start_date ? $request->start_date->toDateString() : 'Not specified',
                'reference_number' => $request->reference_number,
                'qualifications' => $request->qualifications ?? 'Not specified',
                'experience' => $request->experience ?? 'Not specified',
                'job_description' => $request->job_description ?? 'Not specified',
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
                return 'High';
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
            'staff_needed' => $this->formatJsonField($facilityRequest->positions) ?? 'Not specified',
            'urgency' => $this->determinePriority($facilityRequest->priority ?? 'normal'),
            'status' => ucfirst($facilityRequest->status),
            'submitted_at' => $facilityRequest->created_at->diffForHumans(),
            'description' => $facilityRequest->job_description ?? 'Not specified',
            'facility_type' => $this->formatJsonField($facilityRequest->facility_type),
            'employment_type' => $this->formatJsonField($facilityRequest->employment_type),
            'shift_type' => $this->formatJsonField($facilityRequest->shift_type),
            'start_date' => $facilityRequest->start_date ? $facilityRequest->start_date->toDateString() : 'Not specified',
            'reference_number' => $facilityRequest->reference_number,
            'qualifications' => $facilityRequest->qualifications ?? 'Not specified',
            'experience' => $facilityRequest->experience ?? 'Not specified',
            'job_description' => $facilityRequest->job_description ?? 'Not specified',
        ]);
    }

    /**
     * Approve a facility request and move it to tasks table.
     */
    public function approve(FacilityRequest $facilityRequest): JsonResponse
    {
        try {
            // Start a transaction to ensure data integrity
            DB::transaction(function () use ($facilityRequest) {
                // Create a new task with the request's data
                Task::create([
                    'facility_name' => $facilityRequest->facility_name,
                    'contact_person' => $facilityRequest->contact_person,
                    'email' => $facilityRequest->email,
                    'phone' => $facilityRequest->phone,
                    'coordinates' => $facilityRequest->coordinates,
                    'facility_type' => $facilityRequest->facility_type,
                    'positions' => $facilityRequest->positions,
                    'employment_type' => $facilityRequest->employment_type,
                    'shift_type' => $facilityRequest->shift_type,
                    'staff_number' => $facilityRequest->staff_number,
                    'start_date' => $facilityRequest->start_date,
                    'job_requirement_option' => $facilityRequest->job_requirement_option,
                    'qualifications' => $facilityRequest->qualifications,
                    'experience' => $facilityRequest->experience,
                    'job_description' => $facilityRequest->job_description,
                    'job_description_file' => $facilityRequest->job_description_file_path,
                    'reference_number' => $facilityRequest->reference_number,
                    'status' => 'approved',
                    'priority' => $facilityRequest->priority,
                    'confirmed' => $facilityRequest->confirmed,
                    'csrf_token' => $facilityRequest->csrf_token,
                    'submission_date' => $facilityRequest->created_at,
                    'created_at' => $facilityRequest->created_at,
                    'updated_at' => now(),
                ]);

                // Update the request status instead of deleting it
                $facilityRequest->update([
                    'status' => 'approved',
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Request approved and moved to tasks successfully!',
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
                'message' => 'Request rejected successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting request: ' . $e->getMessage(),
            ], 500);
        }
    }
    }
    