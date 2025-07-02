<?php

namespace App\Http\Controllers;

use App\Models\IndividualRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class IndividualRequestController extends Controller
{
    public function index()
    {
        $individualRequests = IndividualRequest::latest()->paginate(10);

        $stats = [
            'pending' => IndividualRequest::where('status', 'pending')->count(),
            'approved' => IndividualRequest::where('status', 'approved')->count(),
            'rejected' => IndividualRequest::where('status', 'rejected')->count(),
            'total' => IndividualRequest::count(),
        ];

        return view('admin._individual', [
            'individualRequests' => $individualRequests,
            'pendingCount' => $stats['pending'],
            'approvedCount' => $stats['approved'],
            'rejectedCount' => $stats['rejected'],
            'totalCount' => $stats['total'],
        ]);
    }

    public function getIndividualRequests(): JsonResponse
    {
        $individualRequests = IndividualRequest::latest()->get()->map(function ($request) {
            return [
                'id' => $request->id,
                'client_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'location' => $request->address ?? 'N/A',
                'service_type' => $request->care_type,
                'care_duration' => $request->schedule ?? 'N/A',
                'urgency' => $request->urgency,
                'status' => ucfirst($request->status),
                'submitted_at' => $request->submission_date ? $request->submission_date->diffForHumans() : 'N/A',
                'description' => $request->description,
                'medical_conditions' => $request->medical_conditions,
                'medications' => $request->medications,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'reference_number' => $request->reference_number,
                'qualifications' => $request->qualifications,
                'experience' => $request->experience,
                'job_description' => $request->job_description,
            ];
        });

        $stats = [
            'pending' => IndividualRequest::where('status', 'pending')->count(),
            'approved' => IndividualRequest::where('status', 'approved')->count(),
            'rejected' => IndividualRequest::where('status', 'rejected')->count(),
            'total' => IndividualRequest::count(),
        ];

        return response()->json([
            'requests' => $individualRequests,
            'stats' => $stats,
        ]);
    }

    public function show(IndividualRequest $individualRequest): JsonResponse
    {
        return response()->json([
            'id' => $individualRequest->id,
            'client_name' => $individualRequest->full_name,
            'email' => $individualRequest->email,
            'phone' => $individualRequest->phone,
            'location' => $individualRequest->address ?? 'N/A',
            'service_type' => $individualRequest->care_type,
            'care_duration' => $individualRequest->schedule ?? 'N/A',
            'urgency' => $individualRequest->urgency,
            'status' => ucfirst($individualRequest->status),
            'submitted_at' => $individualRequest->submission_date ? $individualRequest->submission_date->diffForHumans() : 'N/A',
            'description' => $individualRequest->description,
            'medical_conditions' => $individualRequest->medical_conditions,
            'medications' => $individualRequest->medications,
            'emergency_contact' => $individualRequest->emergency_contact,
            'emergency_phone' => $individualRequest->emergency_phone,
            'reference_number' => $individualRequest->reference_number,
            'qualifications' => $individualRequest->qualifications,
            'experience' => $individualRequest->experience,
            'job_description' => $individualRequest->job_description,
        ]);
    }

    public function approve(IndividualRequest $individualRequest): JsonResponse
    {
        try {
            DB::transaction(function () use ($individualRequest) {
                Task::create([
                    'facility_name' => $individualRequest->full_name, // Client name
                    'contact_person' => $individualRequest->emergency_contact,
                    'email' => $individualRequest->email,
                    'phone' => $individualRequest->phone,
                    'coordinates' => $individualRequest->address,
                    'facility_type' => $individualRequest->care_type,
                    'positions' => 'Caregiver', // Default for individual requests
                    'employment_type' => 'Temporary', // Default
                    'shift_type' => $individualRequest->schedule,
                    'staff_number' => 1, // Typically one caregiver for individual requests
                    'start_date' => now()->toDateString(), // Default to today if no start date
                    'job_requirement_option' => null,
                    'qualifications' => $individualRequest->qualifications,
                    'experience' => $individualRequest->experience,
                    'job_description' => $individualRequest->care_requirements,
                    'job_description_file' => $individualRequest->job_description_file,
                    'reference_number' => $individualRequest->reference_number,
                    'status' => 'approved',
                    'priority' => $individualRequest->urgency,
                    'confirmed' => $individualRequest->confirmed,
                    'csrf_token' => $individualRequest->csrf_token,
                    'submission_date' => $individualRequest->submission_date,
                    'created_at' => $individualRequest->created_at,
                    'updated_at' => $individualRequest->updated_at,
                    'emergency_contact' => $individualRequest->emergency_contact,
                    'emergency_phone' => $individualRequest->emergency_phone,
                    'medical_conditions' => $individualRequest->medical_conditions,
                    'medications' => $individualRequest->medications,
                ]);

                $individualRequest->delete();
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

    public function reject(IndividualRequest $individualRequest): JsonResponse
    {
        try {
            $individualRequest->update(['status' => 'rejected']);

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
