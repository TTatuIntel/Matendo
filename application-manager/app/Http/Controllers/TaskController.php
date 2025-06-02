<?php

namespace App\Http\Controllers;
use App\Models\Application;
use App\Models\FacilityRequest;
use App\Models\IndividualRequest;
use App\Models\TaskAssignment;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    //
public function showAssignList()
{
    $facilities = FacilityRequest::all();
    $individuals = IndividualRequest::all();
    return view('tasks.index', compact('facilities', 'individuals'));
}

public function assignForm($type, $id)
{
    $task = $type === 'facility'
        ? FacilityRequest::findOrFail($id)
        : IndividualRequest::findOrFail($id);

    $workers = Application::where('status', 'approved')->get();

    return view('tasks.assign', compact('task', 'type', 'workers'));
}

public function showAssignForm($type, $id)
{
    if ($type === 'facility') {
        $task = Facility::findOrFail($id);
    } elseif ($type === 'individual') {
        $task = Individual::findOrFail($id);
    } else {
        abort(404);
    }

    $workers = Worker::all(); // ✅ Load workers from DB

    return view('assign', compact('type', 'task', 'workers'));
}

public function assign(Request $request)
{
    $request->validate([
        'task_type' => 'required|in:facility,individual',
        'task_id' => 'required|integer',
        'assigned_to' => 'required|exists:applications,id'
    ]);

    TaskAssignment::create([
        'task_type' => $request->task_type,
        'task_id' => $request->task_id,
        'assigned_to' => $request->assigned_to,
    ]);

    return redirect()->route('requests.index')->with('success', 'Task assigned successfully.');
}
// New method to display task assignments
    public function showAssignments()
{
    // Fetch task assignments with related data
    $assignments = TaskAssignment::with(['user', 'facilityRequest', 'individualRequest'])->get();
    return view('tasks.assignments', compact('assignments'));
}
}
