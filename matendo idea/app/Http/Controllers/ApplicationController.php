<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::latest()->paginate(10);
        return view('admin.partials._applications', compact('applications'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:applications,id',
            'action' => 'required|in:approve,reject',
        ]);

        $application = Application::findOrFail($request->id);

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                // Check if email already exists in users table
                if (User::where('email', $application->email)->exists()) {
                    throw new \Exception('Email already exists in users table');
                }

                // Update application status
                $application->update(['status' => 'approved']);

                // Generate random password
                $plainPassword = Str::random(12);
                $hashedPassword = bcrypt($plainPassword);

                // Create user
                $user = User::create([
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'email' => $application->email,
                    'role' => 'healthworker',
                    'usertype' => 'healthworker',
                    'password' => $hashedPassword,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Delete application
                $application->delete();

                DB::commit();

                return response()->json([
                    'message' => 'Application approved successfully.',
                    'data' => [
                        'email' => $user->email,
                        'password' => $plainPassword,
                    ],
                ]);
            } else {
                // Update application status to rejected
                $application->update(['status' => 'rejected']);
                DB::commit();

                return response()->json(['message' => 'Application rejected successfully']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to process application: ' . $e->getMessage()], 500);
        }
    }
}
