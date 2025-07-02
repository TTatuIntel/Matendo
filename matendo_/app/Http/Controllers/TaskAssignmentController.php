<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use App\Models\FacilityBooking;
use App\Models\IndividualRequest;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskAssignmentController extends Controller
{
    // Assign facility booking
    public function assignFacilityBooking(Request $request, $id)
    {
        $request->validate([
            'assignee_id' => 'required|exists:applications,id'
        ]);

        try {
            DB::beginTransaction();

            // Check if facility booking exists
            $facilityBooking = FacilityBooking::findOrFail($id);

            // Check if already assigned
            if ($facilityBooking->isAssigned()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This facility booking is already assigned.'
                ], 400);
            }

            // Create assignment
            $assignment = TaskAssignment::create([
                'task_type' => 'facility_booking',
                'task_id' => $id,
                'assignee_id' => $request->assignee_id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'status' => 'assigned'
            ]);

            // Update facility booking status if needed
            $facilityBooking->update(['status' => 'assigned']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Facility booking assigned successfully!',
                'assignment' => $assignment->load('assignee')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign facility booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Assign individual request
    public function assignIndividualRequest(Request $request, $id)
    {
        $request->validate([
            'assignee_id' => 'required|exists:applications,id'
        ]);

        try {
            DB::beginTransaction();

            // Check if individual request exists
            $individualRequest = IndividualRequest::findOrFail($id);

            // Check if already assigned
            if ($individualRequest->isAssigned()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This individual request is already assigned.'
                ], 400);
            }

            // Create assignment
            $assignment = TaskAssignment::create([
                'task_type' => 'individual_request',
                'task_id' => $id,
                'assignee_id' => $request->assignee_id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'status' => 'assigned'
            ]);

            // Update individual request status if needed
            $individualRequest->update(['status' => 'assigned']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Individual request assigned successfully!',
                'assignment' => $assignment->load('assignee')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign individual request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get assignment details
    public function getAssignment($taskType, $taskId)
    {
        $assignment = TaskAssignment::where('task_type', $taskType)
            ->where('task_id', $taskId)
            ->with(['assignee', 'assigner'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'assignment' => $assignment
        ]);
    }

    // Update assignment status
    public function updateAssignmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:assigned,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            $assignment = TaskAssignment::findOrFail($id);

            $assignment->update([
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assignment status updated successfully!',
                'assignment' => $assignment->load('assignee')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment: ' . $e->getMessage()
            ], 500);
        }
    }
}
