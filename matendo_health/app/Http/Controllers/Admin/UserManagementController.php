<?php

// app/Http/Controllers/Admin/UserManagementController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::query();

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(20);

            // Get statistics for dashboard cards
            $stats = [
                'doctorsCount' => User::where('role', 'doctor')->count(),
                'patientsCount' => User::where('role', 'patient')->count(),
                'adminsCount' => User::where('role', 'admin')->count(),
                'totalUsers' => User::count(),
                'activeUsers' => User::where('status', 'active')->count(),
                'inactiveUsers' => User::where('status', 'inactive')->count(),
                'newUsersThisMonth' => User::whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->count(),
                'recentlyActiveUsers' => User::where('last_activity', '>=', now()->subDays(7))->count()
            ];

            return view('admin.users', array_merge(compact('users'), $stats));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load users: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'role' => 'required|in:admin,doctor,patient',
                'password' => 'required|min:8',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:active,inactive,pending'
            ]);

            DB::beginTransaction();

            $user = User::create([
                'id' => Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'status' => $request->status ?? 'active',
                'phone' => $request->phone,
                'email_verified_at' => now()
            ]);

            // Create associated profile based on role
            if ($request->role === 'doctor') {
                Doctor::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'license_number' => 'DOC-' . strtoupper(Str::random(8)),
                    'specialization' => 'General Medicine',
                    'verification_status' => 'pending'
                ]);
            } elseif ($request->role === 'patient') {
                Patient::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'medical_record_number' => 'MRN-' . strtoupper(Str::random(10))
                ]);
            }

            // Log user creation
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => "User created: {$user->name} ({$user->role})",
                'causer_id' => auth()->id(),
                'subject_id' => $user->id,
                'properties' => [
                    'user_role' => $user->role,
                    'user_email' => $user->email,
                    'created_by_admin' => true,
                    'ip_address' => request()->ip()
                ]
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'User created successfully',
                'user' => $user->only(['id', 'name', 'email', 'role', 'status'])
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        try {
            // Get role-specific information
            $roleSpecificData = null;
            $stats = [];
            
            switch ($user->role) {
                case 'doctor':
                    if ($user->doctor) {
                        $roleSpecificData = $user->doctor->load(['patients', 'appointments']);
                        $stats = [
                            'total_patients' => $user->doctor->activePatients()->count(),
                            'appointments_this_month' => $user->doctor->appointments()
                                ->whereMonth('scheduled_at', now()->month)->count(),
                            'verification_status' => $user->doctor->verification_status,
                            'specialization' => $user->doctor->specialization
                        ];
                    }
                    break;
                    
                case 'patient':
                    if ($user->patient) {
                        $roleSpecificData = $user->patient->load(['doctors', 'vitalSigns', 'alerts']);
                        $stats = [
                            'assigned_doctors' => $user->patient->doctors()->wherePivot('status', 'active')->count(),
                            'recent_vitals' => $user->patient->vitalSigns()
                                ->where('measured_at', '>=', now()->subWeek())->count(),
                            'active_alerts' => $user->patient->activeAlerts()->count(),
                            'medical_record_number' => $user->patient->medical_record_number
                        ];
                    }
                    break;
                    
                case 'admin':
                    $stats = [
                        'actions_performed' => ActivityLog::where('causer_id', $user->id)
                            ->whereMonth('created_at', now()->month)->count(),
                        'users_managed' => ActivityLog::where('causer_id', $user->id)
                            ->where('log_name', 'user_management')
                            ->whereMonth('created_at', now()->month)->count(),
                        'last_login' => $user->last_activity,
                        'account_status' => $user->status
                    ];
                    break;
            }
            
            // Get recent activity logs
            $recentActivities = ActivityLog::where('causer_id', $user->id)
                ->orWhere('subject_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            // If it's an AJAX request, return JSON (for the edit modal)
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json($user);
            }
            
            // Otherwise return the view
            return view('admin.users.show', compact(
                'user', 
                'roleSpecificData', 
                'stats', 
                'recentActivities'
            ));
            
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading user details: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Error loading user details: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            // Base validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'role' => 'required|in:admin,doctor,patient',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:active,inactive,pending,suspended',
                'password' => 'nullable|min:8'
            ];

            // Add role-specific validation rules
            if ($request->role === 'doctor' || $user->role === 'doctor') {
                $rules = array_merge($rules, [
                    'specialization' => 'nullable|string|max:255',
                    'license_number' => 'nullable|string|max:100',
                    'years_experience' => 'nullable|integer|min:0|max:50',
                    'qualifications' => 'nullable|string|max:1000',
                    'consultation_fee' => 'nullable|numeric|min:0',
                    'accepts_emergency_calls' => 'nullable|boolean'
                ]);
            }

            if ($request->role === 'patient' || $user->role === 'patient') {
                $rules = array_merge($rules, [
                    'date_of_birth' => 'nullable|date|before:today',
                    'gender' => 'nullable|in:male,female,other',
                    'address' => 'nullable|string|max:500',
                    'emergency_contact' => 'nullable|string|max:255',
                    'emergency_phone' => 'nullable|string|max:20',
                    'insurance_number' => 'nullable|string|max:100',
                    'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                    'allergies' => 'nullable|string|max:1000',
                    'chronic_conditions' => 'nullable|string|max:1000'
                ]);
            }

            $request->validate($rules);

            DB::beginTransaction();

            // Update user data
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'phone' => $request->phone,
                'status' => $request->status ?? $user->status
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Handle role-specific updates
            if ($user->role === 'doctor' && $user->doctor) {
                $doctorData = array_filter([
                    'specialization' => $request->specialization,
                    'license_number' => $request->license_number,
                    'years_experience' => $request->years_experience,
                    'qualifications' => $request->qualifications,
                    'consultation_fee' => $request->consultation_fee,
                    'accepts_emergency_calls' => $request->boolean('accepts_emergency_calls', false)
                ], function($value) {
                    return $value !== null && $value !== '';
                });

                if (!empty($doctorData)) {
                    $user->doctor->update($doctorData);
                }
            }

            if ($user->role === 'patient' && $user->patient) {
                $patientData = array_filter([
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'emergency_contact' => $request->emergency_contact,
                    'emergency_phone' => $request->emergency_phone,
                    'insurance_number' => $request->insurance_number,
                    'blood_type' => $request->blood_type,
                    'allergies' => $request->allergies,
                    'chronic_conditions' => $request->chronic_conditions
                ], function($value) {
                    return $value !== null && $value !== '';
                });

                if (!empty($patientData)) {
                    $user->patient->update($patientData);
                }
            }

            // Log user update
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => "User updated: {$user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $user->id,
                'properties' => [
                    'updated_fields' => array_keys($updateData),
                    'updated_by_admin' => true,
                    'user_role' => $user->role,
                    'ip_address' => request()->ip()
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'User updated successfully',
                'user' => $user->only(['id', 'name', 'email', 'role', 'status'])
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            
            // Log user deletion
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => "User deleted: {$user->name}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'deleted_user_id' => $user->id,
                    'deleted_user_email' => $user->email,
                    'deleted_user_role' => $user->role,
                    'ip_address' => request()->ip()
                ]
            ]);
            
            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(User $user)
    {
        try {
            $oldStatus = $user->status;
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            $user->update(['status' => $newStatus]);
            
            // Log status change
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => "User status changed to {$newStatus}: {$user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $user->id,
                'properties' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'ip_address' => request()->ip()
                ]
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => "User status updated to {$newStatus}",
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(User $user)
    {
        try {
            $newPassword = Str::random(12);
            
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
            
            // Log password reset
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => "Password reset for user: {$user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $user->id,
                'properties' => [
                    'reset_by_admin' => true,
                    'ip_address' => request()->ip()
                ]
            ]);
            
            // TODO: Send email with new password
            
            return response()->json([
                'success' => true, 
                'message' => 'Password reset successfully. New password has been sent to user.',
                'temporary_password' => $newPassword // Remove in production, send via email
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verify doctor status
     */
    public function verifyDoctor(User $user)
    {
        try {
            // Check if user is a doctor
            if ($user->role !== 'doctor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only doctors can be verified'
                ], 400);
            }
            
            // Check if doctor profile exists
            if (!$user->doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor profile not found'
                ], 404);
            }
            
            // Update verification status
            $currentStatus = $user->doctor->verification_status;
            $newStatus = $currentStatus === 'verified' ? 'pending' : 'verified';
            
            DB::beginTransaction();
            
            $user->doctor->update([
                'verification_status' => $newStatus,
                'verified_at' => $newStatus === 'verified' ? now() : null,
                'verified_by' => $newStatus === 'verified' ? auth()->id() : null
            ]);
            
            // Log verification action
            ActivityLog::create([
                'log_name' => 'doctor_verification',
                'description' => "Doctor verification status changed to {$newStatus}: {$user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $user->id,
                'properties' => [
                    'old_status' => $currentStatus,
                    'new_status' => $newStatus,
                    'doctor_id' => $user->doctor->id,
                    'ip_address' => request()->ip()
                ]
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Doctor {$newStatus} successfully",
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify doctor: ' . $e->getMessage()
            ], 500);
        }
    }
    public function export(Request $request)
    {
        try {
            $query = User::query();

            // Apply filters similar to index method
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            $users = $query->orderBy('created_at', 'desc')->get();
            
            // Prepare CSV data
            $csvData = [];
            $csvData[] = ['Name', 'Email', 'Role', 'Status', 'Phone', 'Last Login', 'Created At'];
            
            foreach ($users as $user) {
                $csvData[] = [
                    $user->name ?? 'N/A',
                    $user->email ?? 'N/A',
                    ucfirst($user->role ?? 'patient'),
                    ucfirst($user->status ?? 'active'),
                    $user->phone ?? 'N/A',
                    $user->last_activity ? $user->last_activity->format('Y-m-d H:i:s') : 'Never',
                    $user->created_at->format('Y-m-d H:i:s')
                ];
            }
            
            // Generate CSV content
            $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $handle = fopen('php://temp', 'r+');
            
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            
            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);
            
            // Log export action
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => 'Users data exported',
                'causer_id' => auth()->id(),
                'properties' => [
                    'export_count' => $users->count(),
                    'filters_applied' => $request->only(['role', 'status', 'search']),
                    'ip_address' => request()->ip()
                ]
            ]);
            
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to export users: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show bulk actions form
     */
    public function bulkActions()
    {
        return view('admin.users.bulk-actions');
    }
    
    /**
     * Process bulk updates
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:activate,deactivate,delete,update_role',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
                'role' => 'nullable|in:admin,doctor,patient'
            ]);
            
            $userIds = $validated['user_ids'];
            $action = $validated['action'];
            $affectedCount = 0;
            
            DB::beginTransaction();
            
            switch ($action) {
                case 'activate':
                    $affectedCount = User::whereIn('id', $userIds)
                                       ->update(['status' => 'active']);
                    $message = "Activated {$affectedCount} users";
                    break;
                    
                case 'deactivate':
                    $affectedCount = User::whereIn('id', $userIds)
                                       ->update(['status' => 'inactive']);
                    $message = "Deactivated {$affectedCount} users";
                    break;
                    
                case 'delete':
                    $users = User::whereIn('id', $userIds)->get();
                    foreach ($users as $user) {
                        $user->delete();
                        $affectedCount++;
                    }
                    $message = "Deleted {$affectedCount} users";
                    break;
                    
                case 'update_role':
                    if (!$validated['role']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Role is required for role update action'
                        ], 422);
                    }
                    $affectedCount = User::whereIn('id', $userIds)
                                       ->update(['role' => $validated['role']]);
                    $message = "Updated role to {$validated['role']} for {$affectedCount} users";
                    break;
            }
            
            // Log bulk action
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => "Bulk action performed: {$message}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'action' => $action,
                    'user_ids' => $userIds,
                    'affected_count' => $affectedCount,
                    'ip_address' => request()->ip()
                ]
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export users as CSV
     */
    public function exportCsv(Request $request)
    {
        return $this->export($request);
    }
    
    /**
     * Import users from CSV
     */
    public function importCsv(Request $request)
    {
        try {
            $validated = $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048'
            ]);
            
            $file = $validated['csv_file'];
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($csvData);
            
            // Validate CSV headers
            $expectedHeaders = ['name', 'email', 'role', 'status', 'phone'];
            if (array_diff($expectedHeaders, array_map('strtolower', $header))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid CSV format. Required headers: ' . implode(', ', $expectedHeaders)
                ], 422);
            }
            
            $imported = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            foreach ($csvData as $index => $row) {
                try {
                    $userData = array_combine(array_map('strtolower', $header), $row);
                    
                    // Skip if email already exists
                    if (User::where('email', $userData['email'])->exists()) {
                        $errors[] = "Row {$index}: User with email {$userData['email']} already exists";
                        continue;
                    }
                    
                    User::create([
                        'id' => Str::uuid(),
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'role' => $userData['role'] ?? 'patient',
                        'status' => $userData['status'] ?? 'active',
                        'phone' => $userData['phone'] ?? null,
                        'password' => Hash::make('password123'),
                        'email_verified_at' => now()
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: {$e->getMessage()}";
                }
            }
            
            // Log import action
            ActivityLog::create([
                'log_name' => 'user_management',
                'description' => "CSV import completed: {$imported} users imported",
                'causer_id' => auth()->id(),
                'properties' => [
                    'imported_count' => $imported,
                    'error_count' => count($errors),
                    'filename' => $file->getClientOriginalName(),
                    'ip_address' => request()->ip()
                ]
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$imported} users",
                'imported' => $imported,
                'errors' => $errors
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to import CSV: ' . $e->getMessage()
            ], 500);
        }
    }
}
