<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RequestController extends Controller
{
    /**
     * Display facility requests view.
     */
    public function index()
    {
        $facilityRequests = FacilityRequest::latest()->paginate(10);

        $stats = [
            'pending' => FacilityRequest::where('status', 'pending')->count(),
            'approved' => FacilityRequest::where('status', 'approved')->count(),
            'rejected' => FacilityRequest::where('status', 'rejected')->count(),
            'total' => FacilityRequest::count(),
        ];

        return view('admin._facility', [
            'facilityRequests' => $facilityRequests,
            'pendingCount' => $stats['pending'],
            'approvedCount' => $stats['approved'],
            'rejectedCount' => $stats['rejected'],
            'totalCount' => $stats['total'],
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
                'staff_needed' => $request->staff_needed, // Uses model accessor
                'urgency' => $request->urgency, // Uses model accessor
                'status' => ucfirst($request->status),
                'submitted_at' => $request->submission_date->diffForHumans(),
                'description' => $request->description, // Uses model accessor
                'facility_type' => $request->facility_type,
                'employment_type' => $request->employment_type,
                'shift_type' => $request->shift_type,
                'start_date' => $request->start_date->toDateString(),
                'reference_number' => $request->reference_number,
                'qualifications' => $request->qualifications,
                'experience' => $request->experience,
                'job_description' => $request->job_description,
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
            'staff_needed' => $facilityRequest->staff_needed,
            'urgency' => $facilityRequest->urgency,
            'status' => ucfirst($facilityRequest->status),
            'submitted_at' => $facilityRequest->submission_date->diffForHumans(),
            'description' => $facilityRequest->description,
            'facility_type' => $facilityRequest->facility_type,
            'employment_type' => $facilityRequest->employment_type,
            'shift_type' => $facilityRequest->shift_type,
            'start_date' => $facilityRequest->start_date->toDateString(),
            'reference_number' => $facilityRequest->reference_number,
            'qualifications' => $facilityRequest->qualifications,
            'experience' => $facilityRequest->experience,
            'job_description' => $facilityRequest->job_description,
        ]);
    }

    /**
     * Approve a facility request.
     */
    public function approve(FacilityRequest $facilityRequest): JsonResponse
    {
        try {
            $facilityRequest->update(['status' => 'approved']);

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully!',
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
            $facilityRequest->update(['status' => 'rejected']);

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
