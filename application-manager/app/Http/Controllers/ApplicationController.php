<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::all();
        return view('applications.index', compact('applications'));
    }

    public function edit($id)
    {
        $application = Application::findOrFail($id);
        return view('applications.edit', compact('application'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $application = Application::findOrFail($id);
        $application->status = $request->status;
        $application->save();

        if ($request->status === 'approved') {
            $existingUser = DB::table('users')->where('email', $application->email)->first();

            if (!$existingUser) {
                $plainPassword = Str::random(10);
                $hashedPassword = Hash::make($plainPassword);

                DB::table('users')->insert([
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'email' => $application->email,
                    'role' => 'user',
                    'usertype' => 'user',
                    'password' => $hashedPassword,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Log the plaintext password for admin access
                Log::info("User created from application: {$application->email}, Password: {$plainPassword}");

                // Delete application after successful user creation
                $application->delete();
            }
        }

        return redirect()->route('applications.index')->with('success', 'Status updated successfully!');
    }
}
