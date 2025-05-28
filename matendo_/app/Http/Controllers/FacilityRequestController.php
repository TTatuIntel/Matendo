<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use Illuminate\Http\Request;

class FacilityRequestController extends Controller
{
    public function index(Request $request)
    {
        // Fetch facility requests with pagination
        $facilityBookings = FacilityRequest::query()
            ->latest()
            ->paginate(10);

        // Calculate counts for stats
        $unAssignedTasksCount = FacilityRequest::count(); // Total entries in the table
        $assignedCount = 0; // Placeholder as requested
        $rejectedCount = 0; // Placeholder as requested

        // Pass data to the view
        return view('admin.dashboard', [
            'facilityBookings' => $facilityBookings,
            'unAssignedTasksCount' => $unAssignedTasksCount,
            'assignedCount' => $assignedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $facilityRequest = FacilityRequest::findOrFail($id);
        $facilityRequest->status = $request->input('status');
        $facilityRequest->save();

        return redirect()->back()->with('success', 'Facility request status updated successfully.');
    }
}
