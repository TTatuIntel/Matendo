<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Models\Healthworker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class ApplicationController extends Controller
{
    public function index()
    {
        // Fetch only pending applications
        $applications = Application::where('status', 'pending')
            ->latest()
            ->paginate(10);

        // Calculate overview statistics
        $pendingApplications = Application::where('status', 'pending')->count();
        $approvedApplications = Application::where('status', 'approved')->count();
        $rejectedApplications = Application::where('status', 'rejected')->count();
        $oldestPending = Application::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first()?->created_at?->diffForHumans() ?? 'N/A';
        $avgReviewTime = Application::whereIn('status', ['approved', 'rejected'])
            ->avg(DB::raw('DATEDIFF(updated_at, created_at)')) ?? 0;
        $avgReviewTime = round($avgReviewTime, 1) . ' days';
        $newApproved = Application::where('status', 'approved')
            ->whereMonth('updated_at', now()->month)
            ->count();
        $avgOnboardingTime = Healthworker::whereNotNull('verified_at')
            ->avg(DB::raw('DATEDIFF(verified_at, created_at)')) ?? 0;
        $avgOnboardingTime = round($avgOnboardingTime, 1) . ' days';
        $commonRejectionReasons = 'Missing documents, expired licenses';

        return view('admin.partials._applications', compact(
            'applications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'oldestPending',
            'avgReviewTime',
            'newApproved',
            'avgOnboardingTime',
            'commonRejectionReasons'
        ));
    }

    /**
     * Get application details for modal display
     */
    public function getDetails($id)
    {
        try {
            $application = Application::findOrFail($id);

            // Format work preferences for display
            $workTypeDisplay = $this->formatJsonField($application->work_type);
            $shiftTypeDisplay = $this->formatJsonField($application->shift_type);

            // Format start date
            $startDateFormatted = $application->start_date 
                ? $application->start_date->format('F j, Y') 
                : null;

            return response()->json([
                'id' => $application->id,
                'reference_number' => $application->reference_number,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'email' => $application->email,
                'phone' => $application->phone,
                'address' => $application->address,
                'location' => $application->location,
                'profession' => $application->profession,
                'other_profession' => $application->other_profession,
                'specialization' => $application->specialization,
                'years_experience' => $application->years_experience,
                'license_number' => $application->license_number,
                'work_type_display' => $workTypeDisplay,
                'shift_type_display' => $shiftTypeDisplay,
                'preferred_location' => $application->preferred_location,
                'start_date_formatted' => $startDateFormatted,
                
                // Document information
                'resume_name' => $application->resume_name,
                'resume_base64' => !empty($application->resume_base64),
                'license_name' => $application->license_name,
                'license_base64' => !empty($application->license_base64),
                'certifications_name' => $application->certifications_name,
                'certifications_base64' => !empty($application->certifications_base64),
                
                'status' => $application->status,
                'created_at' => $application->created_at->format('F j, Y \a\t g:i A'),
                'updated_at' => $application->updated_at->format('F j, Y \a\t g:i A'),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Application not found'], 404);
        }
    }

    /**
     * Format JSON field for display
     */
    private function formatJsonField($jsonField)
    {
        if (empty($jsonField)) {
            return 'Not specified';
        }

        if (is_string($jsonField)) {
            $decoded = json_decode($jsonField, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return implode(', ', $decoded);
            }
            return $jsonField;
        }

        if (is_array($jsonField)) {
            return implode(', ', $jsonField);
        }

        return 'Not specified';
    }

    /**
     * View a document from base64 data
     */
    public function viewDocument($id, $documentType)
    {
        try {
            $application = Application::findOrFail($id);

            $base64Field = $documentType . '_base64';
            $nameField = $documentType . '_name';
            $mimeField = $documentType . '_mime';

            if (empty($application->$base64Field)) {
                abort(404, 'Document not found');
            }

            $base64Data = $application->$base64Field;
            $fileName = $application->$nameField ?? 'document';
            $mimeType = $application->$mimeField ?? 'application/octet-stream';

            // Decode base64 data
            $fileData = base64_decode($base64Data);

            if ($fileData === false) {
                abort(400, 'Invalid document data');
            }

            return response($fileData, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            abort(404, 'Document not found');
        }
    }

    /**
     * Download a document from base64 data
     */
    public function downloadDocument($id, $documentType)
    {
        try {
            $application = Application::findOrFail($id);

            $base64Field = $documentType . '_base64';
            $nameField = $documentType . '_name';
            $mimeField = $documentType . '_mime';

            if (empty($application->$base64Field)) {
                abort(404, 'Document not found');
            }

            $base64Data = $application->$base64Field;
            $fileName = $application->$nameField ?? 'document';
            $mimeType = $application->$mimeField ?? 'application/octet-stream';

            // Decode base64 data
            $fileData = base64_decode($base64Data);

            if ($fileData === false) {
                abort(400, 'Invalid document data');
            }

            return response($fileData, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            abort(404, 'Document not found');
        }
    }

    /**
     * Export applications to CSV
     */
    public function export()
    {
        try {
            $applications = Application::where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            $csvData = [];
            $csvData[] = [
                'Reference Number',
                'Name',
                'Email',
                'Phone',
                'Profession',
                'Specialization',
                'Years Experience',
                'License Number',
                'Work Type',
                'Shift Type',
                'Preferred Location',
                'Start Date',
                'Submitted Date',
                'Status'
            ];

            foreach ($applications as $application) {
                $csvData[] = [
                    $application->reference_number,
                    $application->first_name . ' ' . $application->last_name,
                    $application->email,
                    $application->phone ?? 'N/A',
                    $application->profession,
                    $application->specialization ?? 'N/A',
                    $application->years_experience ?? 'N/A',
                    $application->license_number ?? 'N/A',
                    $this->formatJsonField($application->work_type),
                    $this->formatJsonField($application->shift_type),
                    $application->preferred_location ?? 'N/A',
                    $application->start_date ? $application->start_date->format('Y-m-d') : 'N/A',
                    $application->created_at->format('Y-m-d H:i:s'),
                    ucfirst($application->status)
                ];
            }

            $filename = 'pending_applications_' . now()->format('Y_m_d_H_i_s') . '.csv';

            $handle = fopen('php://output', 'w');
            
            return response()->stream(
                function() use ($csvData, $handle) {
                    foreach ($csvData as $row) {
                        fputcsv($handle, $row);
                    }
                    fclose($handle);
                },
                200,
                [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export applications: ' . $e->getMessage());
        }
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
                // Check if email already exists
                if (User::where('email', $application->email)->exists()) {
                    throw new \Exception('Email already exists in users table');
                }

                // Generate random password
                $plainPassword = Str::random(12);
                $hashedPassword = bcrypt($plainPassword);

                // Create user
                $user = User::create([
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'email' => $application->email,
                    'usertype' => 'healthworker',
                    'password' => $hashedPassword,
                    'email_verified_at' => now(),
                ]);

                // Process and save documents
                $documentPaths = $this->processApplicationDocuments($application);

                // Generate PDF snapshot
                $snapshotPath = $this->generateApplicationSnapshot($application, $user);

                // Create healthworker record with proper field mapping
                $healthworker = Healthworker::create([
                    // Foreign Keys
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                    
                    // Personal Information (from application)
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'email' => $application->email,
                    'phone' => $application->phone,
                    'address' => $application->address,
                    
                    // Professional Information (from application)
                    'profession' => $application->profession,
                    'specialization' => $application->specialization,
                    'specialty' => $application->specialization, // Alias for specialization
                    'years_experience' => $application->years_experience,
                    'license_number' => $application->license_number,
                    
                    // Document References (file paths)
                    'resume_path' => $documentPaths['resume'],
                    'license_path' => $documentPaths['license'],
                    'certifications_path' => $documentPaths['certifications'],
                    'application_snapshot_path' => $snapshotPath,
                    
                    // Work Preferences (from application)
                    'work_type' => $application->work_type,
                    'shift_type' => $application->shift_type,
                    'preferred_location' => $application->preferred_location,
                    'available_from' => $application->start_date,
                    
                    // Status and Verification
                    'status' => 'active',
                    'verified' => true,
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                    
                    // Performance tracking (defaults)
                    'tasks_completed' => 0,
                    'rating' => null,
                    'total_ratings' => 0,
                ]);

                // Update application status
                $application->update([
                    'status' => 'approved',
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Application approved successfully.',
                    'data' => [
                        'email' => $user->email,
                        'password' => $plainPassword,
                        'snapshot_url' => $snapshotPath ? Storage::url($snapshotPath) : null,
                        'pdf_url' => $snapshotPath ? Storage::url($snapshotPath) : null,
                        'healthworker_id' => $healthworker->id,
                        'user_id' => $user->id,
                    ],
                ]);
                
            } else { // reject
                // Update application status with rejection
                $application->update([
                    'status' => 'rejected',
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                    'rejection_reason' => $request->input('reason', 'Application does not meet requirements'),
                ]);

                DB::commit();

                return response()->json(['message' => 'Application rejected successfully']);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to process application: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Process and save application documents from base64 to files
     */
    private function processApplicationDocuments(Application $application)
    {
        $documentPaths = [
            'resume' => null,
            'license' => null,
            'certifications' => null,
        ];

        // Process Resume
        if ($application->resume_base64 && $application->resume_name) {
            $documentPaths['resume'] = $this->saveBase64Document(
                $application->resume_base64,
                $application->resume_name,
                $application->resume_mime,
                'healthworker-resumes',
                $application->reference_number
            );
        }

        // Process License
        if ($application->license_base64 && $application->license_name) {
            $documentPaths['license'] = $this->saveBase64Document(
                $application->license_base64,
                $application->license_name,
                $application->license_mime,
                'healthworker-licenses',
                $application->reference_number
            );
        }

        // Process Certifications
        if ($application->certifications_base64 && $application->certifications_name) {
            $documentPaths['certifications'] = $this->saveBase64Document(
                $application->certifications_base64,
                $application->certifications_name,
                $application->certifications_mime,
                'healthworker-certifications',
                $application->reference_number
            );
        }

        return $documentPaths;
    }

    /**
     * Save base64 encoded document as file
     */
    private function saveBase64Document($base64Data, $originalName, $mimeType, $folder, $referenceNumber)
    {
        try {
            // Decode base64 data
            $decodedData = base64_decode($base64Data);
            
            // Get file extension from original name or mime type
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            if (empty($extension)) {
                $extension = $this->getExtensionFromMimeType($mimeType);
            }
            
            // Generate unique filename
            $filename = $referenceNumber . '_' . time() . '_' . Str::random(8) . '.' . $extension;
            $filePath = $folder . '/' . $filename;
            
            // Save file to storage
            Storage::put($filePath, $decodedData);
            
            return $filePath;
            
        } catch (\Exception $e) {
            \Log::error("Failed to save document: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get file extension from mime type
     */
    private function getExtensionFromMimeType($mimeType)
    {
        $mimeToExtension = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'text/plain' => 'txt',
        ];

        return $mimeToExtension[$mimeType] ?? 'bin';
    }

    /**
     * Generate PDF snapshot of application
     */
    private function generateApplicationSnapshot(Application $application, User $user)
    {
        try {
            $pdf = Pdf::loadView('admin.application_snapshot', [
                'application' => $application,
                'user' => $user,
                'generated_at' => now(),
            ]);
            
            $filename = 'application_snapshot_' . $application->reference_number . '.pdf';
            $filePath = 'application-snapshots/' . $filename;
            
            Storage::put($filePath, $pdf->output());
            
            return $filePath;
            
        } catch (\Exception $e) {
            \Log::error("Failed to generate application snapshot: " . $e->getMessage());
            return null;
        }
    }

    public function regeneratePassword(Request $request, $reference_number)
    {
        $application = Application::where('reference_number', $reference_number)->firstOrFail();
        $user = User::where('email', $application->email)->firstOrFail();

        try {
            DB::beginTransaction();

            $plainPassword = Str::random(12);
            $user->update([
                'password' => bcrypt($plainPassword),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Password regenerated successfully.',
                'password' => $plainPassword,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to regenerate password: ' . $e->getMessage()], 500);
        }
    }
}