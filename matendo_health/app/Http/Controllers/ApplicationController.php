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
use Illuminate\Support\Facades\Response;

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
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Generate PDF snapshot
                $pdf = Pdf::loadView('admin.application_snapshot', [
                    'application' => $application,
                    'user' => $user,
                ]);
                $pdfPath = 'snapshots/application_' . $application->reference_number . '.pdf';
                Storage::put($pdfPath, $pdf->output());

                // Create healthworker record
                $healthworker = Healthworker::create([
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'specialty' => $application->specialization,
                    'resume' => $application->resume,
                    'license_doc' => $application->license_doc,
                    'certifications' => $application->certifications,
                    'application_snapshot_pdf' => $pdfPath,
                    'status' => 'active',
                    'verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update application status
                $application->update(['status' => 'approved']);

                DB::commit();

                return response()->json([
                    'message' => 'Application approved successfully.',
                    'data' => [
                        'email' => $user->email,
                        'password' => $plainPassword,
                        'snapshot_url' => Storage::url($pdfPath),
                        'pdf_url' => Storage::url($pdfPath),
                    ],
                ]);
            } else {
                // Soft-delete the application
                $application->delete();

                DB::commit();

                return response()->json(['message' => 'Application rejected successfully']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to process application: ' . $e->getMessage()], 500);
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
                'updated_at' => now(),
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

    /**
     * View document in browser
     */
    public function viewDocument(Request $request, $applicationId, $documentType)
    {
        $application = Application::findOrFail($applicationId);
        
        $documentPath = null;
        switch ($documentType) {
            case 'resume':
                $documentPath = $application->resume;
                break;
            case 'license':
                $documentPath = $application->license_doc;
                break;
            case 'certifications':
                $documentPath = $application->certifications;
                break;
            default:
                abort(404, 'Document type not found');
        }

        if (!$documentPath || !Storage::exists($documentPath)) {
            abort(404, 'Document not found');
        }

        $file = Storage::get($documentPath);
        $mimeType = Storage::mimeType($documentPath);
        $filename = basename($documentPath);

        return Response::make($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /**
     * Download document
     */
    public function downloadDocument(Request $request, $applicationId, $documentType)
    {
        $application = Application::findOrFail($applicationId);
        
        $documentPath = null;
        $documentName = null;
        
        switch ($documentType) {
            case 'resume':
                $documentPath = $application->resume;
                $documentName = 'resume_' . $application->reference_number;
                break;
            case 'license':
                $documentPath = $application->license_doc;
                $documentName = 'license_' . $application->reference_number;
                break;
            case 'certifications':
                $documentPath = $application->certifications;
                $documentName = 'certifications_' . $application->reference_number;
                break;
            default:
                abort(404, 'Document type not found');
        }

        if (!$documentPath || !Storage::exists($documentPath)) {
            abort(404, 'Document not found');
        }

        $fileExtension = pathinfo($documentPath, PATHINFO_EXTENSION);
        $filename = $documentName . '.' . $fileExtension;

        return Storage::download($documentPath, $filename);
    }

    /**
     * Export applications to CSV
     */
    public function export(Request $request)
    {
        $applications = Application::where('status', 'pending')->get();
        
        $filename = 'pending_applications_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Reference Number',
                'Name',
                'Email',
                'Phone',
                'Profession',
                'Specialization',
                'Experience (Years)',
                'Work Type',
                'Shift Type',
                'Preferred Location',
                'Submitted Date',
                'Documents'
            ]);

            // CSV data
            foreach ($applications as $application) {
                $documents = [];
                if ($application->resume) $documents[] = 'Resume';
                if ($application->license_doc) $documents[] = 'License';
                if ($application->certifications) $documents[] = 'Certifications';
                
                fputcsv($file, [
                    $application->reference_number,
                    $application->first_name . ' ' . $application->last_name,
                    $application->email,
                    $application->phone,
                    $application->profession,
                    $application->specialization ?? 'N/A',
                    $application->years_experience ?? 'N/A',
                    $application->work_type ?? 'N/A',
                    $application->shift_type ?? 'N/A',
                    $application->preferred_location ?? 'N/A',
                    $application->created_at->format('Y-m-d H:i:s'),
                    implode(', ', $documents)
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}