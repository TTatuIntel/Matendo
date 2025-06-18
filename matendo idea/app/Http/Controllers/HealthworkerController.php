<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthWorkerController extends Controller
{

public function index()
    {
    $applications = []; // Define $applications, e.g., fetch from a model or set as empty array
        return view('dashboard', compact('applications'));
    }
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

    // public function show($id)
    // {
    //     $worker = DB::table('users')
    //         ->where('id', $id)
    //         ->where('usertype', 'healthworker')
    //         ->select('id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at')
    //         ->first();

    //     return response()->json($worker);
    // }

    // public function toggleVerification(Request $request, $id)
    // {
    //     $worker = DB::table('users')->where('id', $id)->where('usertype', 'healthworker')->first();

    //     if (!$worker) {
    //         return response()->json(['success' => false, 'message' => 'Health worker not found'], 404);
    //     }

    //     DB::table('users')
    //         ->where('id', $id)
    //         ->update(['email_verified_at' => $worker->email_verified_at ? null : now()]);

    //     return response()->json(['success' => true, 'message' => 'Verification status updated']);
    // }

    // public function create()
    // {
    //     return view('health_workers.create');
    // }

    // public function edit($id)
    // {
    //     $worker = DB::table('users')->where('id', $id)->first();
    //     return view('health_workers.edit', compact('worker'));
    // }

    // public function destroy($id)
    // {
    //     DB::table('users')->where('id', $id)->delete();
    //     return redirect()->route('health-workers.index')->with('success', 'Health worker deleted successfully.');
    // }
}
