<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\FacilityRequest;
use App\Models\IndividualRequest;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
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

        // ✅ Fetch users with role = user and usertype = user
        $workers = User::where('role', 'user')
                       ->where('usertype', 'user')
                       ->get();

        return view('tasks.assign', compact('task', 'type', 'workers'));
    }

    // Optional legacy method — safe to remove if unused
    public function showAssignForm($type, $id)
    {
        if ($type === 'facility') {
            $task = Facility::findOrFail($id);
        } elseif ($type === 'individual') {
            $task = Individual::findOrFail($id);
        } else {
            abort(404);
        }

        $workers = Worker::all(); // Legacy logic — possibly outdated

        return view('assign', compact('type', 'task', 'workers'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'task_type' => 'required|in:facility,individual',
            'task_id' => 'required|integer',
            'assigned_to' => 'required|exists:users,id' // ✅ Validate against users table
        ]);

        TaskAssignment::create([
            'task_type' => $request->task_type,
            'task_id' => $request->task_id,
            'assigned_to' => $request->assigned_to,
        ]);

        return redirect()->route('requests.index')->with('success', 'Task assigned successfully.');
    }

    public function showAssignments()
    {
        $assignments = TaskAssignment::with(['user', 'facilityRequest', 'individualRequest'])->get();
        return view('tasks.assignments', compact('assignments'));
    }
}
