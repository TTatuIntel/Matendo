<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Models\Doctor;
use App\Models\VitalSign;
use App\Models\MedicalRecord;
use App\Models\Alert;
use App\Models\DoctorPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientManagementController extends Controller
{
    /**
     * Display a listing of patients with advanced filtering
     */
    public function index(Request $request)
    {
        try {
            $query = Patient::with(['user', 'doctors.user', 'latestVitalSigns']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('medical_record_number', 'like', "%{$search}%");
            }

            // Status filtering
            if ($request->filled('status')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            // Doctor filtering
            if ($request->filled('doctor_id')) {
                $query->whereHas('doctors', function($q) use ($request) {
                    $q->where('doctor_id', $request->doctor_id);
                });
            }

            // Date range filtering
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->date_from)->startOfDay(),
                    Carbon::parse($request->date_to)->endOfDay()
                ]);
            }

            // Critical patients filter
            if ($request->filled('critical_only') && $request->critical_only) {
                $query->whereHas('alerts', function($q) {
                    $q->where('severity', 'critical')
                      ->where('status', 'active');
                });
            }

            $patients = $query->orderBy('created_at', 'desc')->paginate(20);

            // Get statistics that match blade template variables
            $totalPatients = Patient::count();
            $activePatients = Patient::whereHas('user', function($q) {
                $q->where('status', 'active');
            })->count();
            $criticalPatients = Patient::whereHas('alerts', function($q) {
                $q->where('severity', 'critical')
                  ->where('status', 'active');
            })->count();
            $vitalRecordsToday = VitalSign::whereDate('created_at', today())->count();

            // Additional stats for internal use
            $stats = [
                'total' => $totalPatients,
                'active' => $activePatients,
                'critical' => $criticalPatients,
                'unassigned' => Patient::whereDoesntHave('doctors')->count(),
                'vitals_today' => $vitalRecordsToday
            ];

            // Get available doctors for assignment
            $doctors = Doctor::whereHas('user', function($q) {
                $q->where('status', 'active');
            })->with('user')->get();

            return view('admin.patients', compact(
                'patients', 
                'totalPatients', 
                'activePatients', 
                'criticalPatients', 
                'vitalRecordsToday',
                'stats', 
                'doctors'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load patients: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        try {
            $patient->load([
                'user',
                'doctors.user',
                'vitalSigns' => function($q) {
                    $q->orderBy('measured_at', 'desc')->limit(10);
                },
                'medicalRecords' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(5);
                },
                'alerts' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(5);
                }
            ]);

            return response()->json([
                'success' => true,
                'patient' => $patient
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load patient details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign doctor to patient
     */
    public function assignDoctor(Request $request, Patient $patient)
    {
        try {
            $validated = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'relationship_type' => 'required|in:primary,secondary,consulting,specialist,referral',
                'notes' => 'nullable|string|max:500'
            ]);

            // Check if relationship already exists
            $existing = DoctorPatient::where('doctor_id', $validated['doctor_id'])
                                   ->where('patient_id', $patient->id)
                                   ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor is already assigned to this patient'
                ], 422);
            }

            DoctorPatient::create([
                'id' => Str::uuid(),
                'doctor_id' => $validated['doctor_id'],
                'patient_id' => $patient->id,
                'relationship_type' => $validated['relationship_type'],
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
                'status' => 'active',
                'notes' => $validated['notes'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor assigned successfully'
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
                'message' => 'Failed to assign doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove doctor from patient
     */
    public function removeDoctor(Patient $patient, Doctor $doctor)
    {
        try {
            $relationship = DoctorPatient::where('doctor_id', $doctor->id)
                                       ->where('patient_id', $patient->id)
                                       ->first();

            if (!$relationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor is not assigned to this patient'
                ], 404);
            }

            $relationship->update([
                'status' => 'inactive',
                'removed_at' => now(),
                'removed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient medical history
     */
    public function medicalHistory(Patient $patient)
    {
        try {
            $history = [
                'vital_signs' => $patient->vitalSigns()
                                        ->orderBy('measured_at', 'desc')
                                        ->limit(50)
                                        ->get(),
                'medical_records' => $patient->medicalRecords()
                                            ->with('doctor.user')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(20)
                                            ->get(),
                'alerts' => $patient->alerts()
                                   ->orderBy('created_at', 'desc')
                                   ->limit(30)
                                   ->get()
            ];

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load medical history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update patient profile
     */
    public function updateProfile(Request $request, Patient $patient)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $patient->user_id,
                'phone' => 'nullable|string|max:20',
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

            DB::beginTransaction();

            // Update user information
            $patient->user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            // Update patient-specific information
            $patientData = array_intersect_key($validated, array_flip([
                'phone', 'date_of_birth', 'gender', 'address',
                'emergency_contact', 'emergency_phone', 'insurance_number',
                'blood_type', 'allergies', 'chronic_conditions'
            ]));

            $patient->update($patientData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient profile updated successfully'
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
                'message' => 'Failed to update patient profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient alerts
     */
    public function alerts(Patient $patient)
    {
        try {
            $alerts = $patient->alerts()
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);

            return response()->json([
                'success' => true,
                'alerts' => $alerts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load patient alerts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export patients data
     */
    public function export(Request $request)
    {
        try {
            $patients = Patient::with(['user', 'doctors.user'])
                             ->get();

            return response()->streamDownload(function () use ($patients) {
                echo "Patient Export Report\n";
                echo "Generated: " . now() . "\n\n";
                echo "Name,Email,Medical Record Number,Status,Assigned Doctors,Created At\n";
                
                foreach ($patients as $patient) {
                    $doctors = $patient->doctors->pluck('user.name')->implode('; ');
                    echo implode(',', [
                        '"' . $patient->user->name . '"',
                        $patient->user->email,
                        $patient->medical_record_number,
                        $patient->user->status,
                        '"' . $doctors . '"',
                        $patient->created_at->format('Y-m-d H:i:s')
                    ]) . "\n";
                }
            }, 'patients-export-' . now()->format('Y-m-d') . '.csv');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to export patients: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a patient
     */
    public function destroy(Patient $patient)
    {
        try {
            DB::beginTransaction();

            // Deactivate all doctor relationships
            $patient->doctorPatients()->update([
                'status' => 'inactive',
                'removed_at' => now(),
                'removed_by' => auth()->id()
            ]);

            // Soft delete the patient
            $patient->delete();

            // Optionally soft delete the user as well if no other role exists
            if ($patient->user && $patient->user->role === 'patient') {
                $patient->user->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete patient: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions for patients
     */
    public function bulkAction(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:activate,deactivate,assign_doctor,export',
                'patient_ids' => 'required|array',
                'patient_ids.*' => 'exists:patients,id',
                'doctor_id' => 'required_if:action,assign_doctor|exists:doctors,id'
            ]);

            $patients = Patient::whereIn('id', $validated['patient_ids'])->get();
            $count = $patients->count();

            switch ($validated['action']) {
                case 'activate':
                    User::whereIn('id', $patients->pluck('user_id'))
                        ->update(['status' => 'active']);
                    $message = "{$count} patients activated successfully";
                    break;

                case 'deactivate':
                    User::whereIn('id', $patients->pluck('user_id'))
                        ->update(['status' => 'inactive']);
                    $message = "{$count} patients deactivated successfully";
                    break;

                case 'assign_doctor':
                    foreach ($patients as $patient) {
                        DoctorPatient::firstOrCreate([
                            'doctor_id' => $validated['doctor_id'],
                            'patient_id' => $patient->id
                        ], [
                            'id' => Str::uuid(),
                            'relationship_type' => 'secondary',
                            'assigned_at' => now(),
                            'assigned_by' => auth()->id(),
                            'status' => 'active'
                        ]);
                    }
                    $message = "Doctor assigned to {$count} patients successfully";
                    break;

                default:
                    $message = "Unknown action";
            }

            return response()->json([
                'success' => true,
                'message' => $message
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
                'message' => 'Bulk action failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
