<?php


namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class HealthWorkerController extends Controller
{
    // public function index()
    // {
    //     $health_workers = DB::table('users')
    //         ->where('usertype', 'healthworker')
    //         ->select('id', 'name', 'email', 'email_verified_at', 'created_at')
    //         ->get();

    //     $stats = [
    //         'total' => $health_workers->count(),
    //         'verified' => $health_workers->whereNotNull('email_verified_at')->count(),
    //         'unverified' => $health_workers->whereNull('email_verified_at')->count(),
    //     ];

    //     return response()->json([
    //         'health_workers' => $health_workers,
    //         'stats' => $stats,
    //     ]);
    // }

// public function index()
// {
//     $health_workers = DB::table('users')
//         ->where('usertype', 'healthworker')
//         ->select('id', 'name', 'email', 'email_verified_at', 'created_at')
//         ->get();

//     $stats = [
//         'total' => $health_workers->count(),
//         'verified' => $health_workers->whereNotNull('email_verified_at')->count(),
//         'unverified' => $health_workers->whereNull('email_verified_at')->count(),
//     ];

// // Fetch tasks assigned to the logged-in user
//      // Fetch tasks assigned to the logged-in user with the correct column name
//         $userId = Auth::id(); // Get the authenticated user's ID
//         $assignedTasks = Task::where('assigned_to', $userId)->get(); // Replace 'assigned_to' with the correct column
//     // Return the view with data instead of JSON
//     return view('healthworker.dashboard', [
//         'health_workers' => $health_workers,
//         'stats' => $stats,
//          'assignedTasks' => $assignedTasks
//     ]);
// }


// public function index()
// {

//     $health_workers = DB::table('users')
//         ->where('usertype', 'healthworker')
//         ->select('id', 'name', 'email', 'email_verified_at', 'created_at')
//         ->get();

//     $stats = [
//         'total' => $health_workers->count(),
//         'verified' => $health_workers->whereNotNull('email_verified_at')->count(),
//         'unverified' => $health_workers->whereNull('email_verified_at')->count(),
//     ];

//     return response()->json([
//         'health_workers' => $health_workers,
//         'stats' => $stats,
//     ]);
// }

public function index()
{
    $health_workers = DB::table('users')
        ->where('usertype', 'healthworker')
        ->select('id', 'name', 'email', 'email_verified_at', 'created_at')
        ->get();

    $stats = [
        'total' => $health_workers->count(),
        'verified' => $health_workers->whereNotNull('email_verified_at')->count(),
        'unverified' => $health_workers->whereNull('email_verified_at')->count(),
    ];

    // Check if the user is an admin or health worker
    if (auth()->user()->usertype === 'admin') {
        // Return JSON for admin dashboard
        return response()->json([
            'health_workers' => $health_workers,
            'stats' => $stats,
        ]);
    } else {
        // Return view for health worker dashboard
        $userId = Auth::id();
        $assignedTasks = Task::where('assigned_to', $userId)->get();

        return view('healthworker.dashboard', [
            'health_workers' => $health_workers, // Optional: include if needed in health worker view
            'stats' => $stats,                   // Optional: include if needed in health worker view
            'assignedTasks' => $assignedTasks


        ]);
    }
}
    public function show($id)
    {
        $worker = DB::table('users')
            ->where('id', $id)
            ->where('usertype', 'healthworker')
            ->select('id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at')
            ->first();

        return response()->json($worker);
    }

    public function toggleVerification(Request $request, $id)
    {
        $worker = DB::table('users')->where('id', $id)->where('usertype', 'healthworker')->first();

        if (!$worker) {
            return response()->json(['success' => false, 'message' => 'Health worker not found'], 404);
        }

        DB::table('users')
            ->where('id', $id)
            ->update(['email_verified_at' => $worker->email_verified_at ? null : now()]);

        return response()->json(['success' => true, 'message' => 'Verification status updated']);
    }

    public function create()
    {
        return view('health_workers.create');
    }

    public function edit($id)
    {
        $worker = DB::table('users')->where('id', $id)->first();
        return view('health_workers.edit', compact('worker'));
    }

    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return redirect()->route('health-workers.index')->with('success', 'Health worker deleted successfully.');
    }


// public function completeTask($taskId)
// {
//     try {
//         $task = Task::findOrFail($taskId);

//         // Verify the task is assigned to the current user
//         if ($task->assigned_to != auth()->id()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'You are not authorized to complete this task'
//             ], 403);
//         }

//         // Update the task status
//         $task->update([
//             'status' => 'completed',
//             'completed_at' => now()
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Task marked as completed successfully!'
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error completing task: ' . $e->getMessage()
//         ], 500);
//     }
// }



// Add this method to your TaskController class

public function completeTask(Request $request, $taskId)
{
    try {
        // Find the task and ensure it belongs to the authenticated user
        $task = Task::where('id', $taskId)
                   ->where('assigned_to', auth()->id()) // Ensure user can only complete their own tasks
                   ->firstOrFail();

        // Update the task status to completed
        $task->update([
            'status' => 'completed',
            'completed_at' => now(), // Optional: track when it was completed
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

// Also, make sure your dashboard method filters out completed tasks
public function dashboard()
{
    // Get only non-completed tasks assigned to the authenticated user
    $assignedTasks = Task::where('assigned_to', auth()->id())
                        ->where('status', '!=', 'completed') // Exclude completed tasks
                        ->orderBy('created_at', 'desc')
                        ->get();

    return view('dashboard', compact('assignedTasks'));
}
}

