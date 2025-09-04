<?php

// app/Http/Controllers/Admin/DoctorManagementController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\DoctorPatient;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DoctorManagementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Doctor::with(['user', 'doctorPatients']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('license_number', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            }

            // Status filtering
            if ($request->filled('status')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            // Verification status filtering
            if ($request->filled('verification_status')) {
                $query->where('verification_status', $request->verification_status);
            }

            // Specialization filtering
            if ($request->filled('specialization')) {
                $query->where('specialization', 'like', "%{$request->specialization}%");
            }

            $doctors = $query->withCount('doctorPatients as patients_count')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

            // Get statistics for dashboard
            $totalDoctors = Doctor::count();
            $activeDoctors = Doctor::whereHas('user', function($q) {
                $q->where('status', 'active');
            })->count();
            $pendingVerifications = Doctor::where('verification_status', 'pending')->count();
            $totalPatients = Patient::count();

            return view('admin.doctors', compact(
                'doctors', 
                'totalDoctors', 
                'activeDoctors', 
                'pendingVerifications', 
                'totalPatients'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load doctors: ' . $e->getMessage()]);
        }
    }

    public function show(Doctor $doctor)
    {
        try {
            $doctor->load([
                'user',
                'verifiedBy',
                'doctorPatients.patient.user',
                'medicalRecords' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(10);
                },
                'appointments' => function($q) {
                    $q->orderBy('scheduled_at', 'desc')->limit(10);
                }
            ]);

            $stats = [
                'total_patients' => $doctor->doctorPatients()->count(),
                'active_patients' => $doctor->doctorPatients()->where('status', 'active')->count(),
                'appointments_this_month' => $doctor->appointments()
                    ->whereMonth('scheduled_at', now()->month)
                    ->whereYear('scheduled_at', now()->year)
                    ->count(),
                'medical_records_created' => $doctor->medicalRecords()->count()
            ];

            return response()->json([
                'success' => true,
                'doctor' => $doctor,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load doctor details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verify(Doctor $doctor)
    {
        try {
            $doctor->update([
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            // Log verification
            ActivityLog::create([
                'log_name' => 'doctor_management',
                'description' => "Doctor verified: {$doctor->user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $doctor->id,
                'properties' => [
                    'doctor_id' => $doctor->id,
                    'license_number' => $doctor->license_number,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json(['success' => true, 'message' => 'Doctor verified successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Doctor $doctor)
    {
        try {
            $doctor->update([
                'verification_status' => 'rejected',
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            // Log rejection
            ActivityLog::create([
                'log_name' => 'doctor_management',
                'description' => "Doctor verification rejected: {$doctor->user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $doctor->id,
                'properties' => [
                    'doctor_id' => $doctor->id,
                    'license_number' => $doctor->license_number,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json(['success' => true, 'message' => 'Doctor verification rejected']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignPatients(Request $request, Doctor $doctor)
    {
        try {
            $validated = $request->validate([
                'patient_ids' => 'required|array',
                'patient_ids.*' => 'exists:patients,id',
                'relationship_type' => 'required|in:primary,secondary,consulting,specialist,referral'
            ]);

            DB::beginTransaction();

            $assignedCount = 0;
            foreach ($validated['patient_ids'] as $patientId) {
                $existing = DoctorPatient::where('doctor_id', $doctor->id)
                                        ->where('patient_id', $patientId)
                                        ->first();

                if (!$existing) {
                    DoctorPatient::create([
                        'id' => Str::uuid(),
                        'doctor_id' => $doctor->id,
                        'patient_id' => $patientId,
                        'relationship_type' => $validated['relationship_type'],
                        'assigned_at' => now(),
                        'assigned_by' => auth()->id(),
                        'status' => 'active'
                    ]);
                    $assignedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => "{$assignedCount} patients assigned successfully"
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
                'message' => 'Failed to assign patients: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removePatient(Doctor $doctor, Patient $patient)
    {
        try {
            $relationship = DoctorPatient::where('doctor_id', $doctor->id)
                                       ->where('patient_id', $patient->id)
                                       ->first();

            if (!$relationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient is not assigned to this doctor'
                ], 404);
            }

            $relationship->update([
                'status' => 'inactive',
                'removed_at' => now(),
                'removed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patient removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove patient: ' . $e->getMessage()
            ], 500);
        }
    }

    public function patients(Doctor $doctor)
    {
        try {
            $patients = $doctor->patients()
                              ->with('user')
                              ->wherePivot('status', 'active')
                              ->paginate(20);

            return response()->json([
                'success' => true,
                'patients' => $patients
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load doctor patients: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSpecialization(Request $request, Doctor $doctor)
    {
        try {
            $validated = $request->validate([
                'specialization' => 'required|string|max:255',
                'qualifications' => 'nullable|string|max:1000',
                'years_experience' => 'nullable|integer|min:0|max:50',
                'consultation_fee' => 'nullable|numeric|min:0',
                'accepts_emergency_calls' => 'boolean'
            ]);

            $doctor->update($validated);

            // Log update
            ActivityLog::create([
                'log_name' => 'doctor_management',
                'description' => "Doctor specialization updated: {$doctor->user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $doctor->id,
                'properties' => [
                    'old_specialization' => $doctor->getOriginal('specialization'),
                    'new_specialization' => $validated['specialization'],
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor specialization updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update specialization: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Doctor $doctor)
    {
        try {
            DB::beginTransaction();

            // Deactivate all patient relationships
            $doctor->doctorPatients()->update([
                'status' => 'inactive',
                'removed_at' => now(),
                'removed_by' => auth()->id()
            ]);

            // Soft delete the doctor
            $doctor->delete();

            // Log deletion
            ActivityLog::create([
                'log_name' => 'doctor_management',
                'description' => "Doctor deleted: {$doctor->user->name}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'deleted_doctor_id' => $doctor->id,
                    'license_number' => $doctor->license_number,
                    'specialization' => $doctor->specialization,
                    'ip_address' => request()->ip()
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Doctor deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Doctor $doctor)
    {
        try {
            $oldStatus = $doctor->user->status;
            $newStatus = $doctor->user->status === 'active' ? 'inactive' : 'active';
            
            $doctor->user->update(['status' => $newStatus]);
            
            // Log status change
            ActivityLog::create([
                'log_name' => 'doctor_management',
                'description' => "Doctor status changed to {$newStatus}: {$doctor->user->name}",
                'causer_id' => auth()->id(),
                'subject_id' => $doctor->id,
                'properties' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'doctor_id' => $doctor->id,
                    'ip_address' => request()->ip()
                ]
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => "Doctor status updated to {$newStatus}",
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update doctor status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $doctors = Doctor::with(['user', 'doctorPatients.patient.user'])->get();

            return response()->streamDownload(function () use ($doctors) {
                echo "Doctor Export Report\n";
                echo "Generated: " . now() . "\n\n";
                echo "Name,Email,License Number,Specialization,Status,Verification Status,Patients Count,Created At\n";
                
                foreach ($doctors as $doctor) {
                    $patientsCount = $doctor->doctorPatients->count();
                    echo implode(',', [
                        '"' . ($doctor->user->name ?? 'Unknown') . '"',
                        $doctor->user->email ?? 'No email',
                        $doctor->license_number ?? 'No license',
                        '"' . ($doctor->specialization ?? 'General') . '"',
                        $doctor->user->status ?? 'active',
                        $doctor->verification_status ?? 'pending',
                        $patientsCount,
                        $doctor->created_at->format('Y-m-d H:i:s')
                    ]) . "\n";
                }
            }, 'doctors-export-' . now()->format('Y-m-d') . '.csv');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to export doctors: ' . $e->getMessage()]);
        }
    }
}
