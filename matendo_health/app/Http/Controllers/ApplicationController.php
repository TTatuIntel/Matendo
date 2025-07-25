<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Models\Healthworker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Display pending applications and overview stats.
     */
    public function index()
    {
        $applications = Application::where('status', 'pending')
            ->latest()
            ->paginate(10);

        $pendingApplications    = Application::where('status', 'pending')->count();
        $approvedApplications   = Application::where('status', 'approved')->count();
        $rejectedApplications   = Application::where('status', 'rejected')->count();
        $oldestPending          = Application::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first()?->created_at?->diffForHumans() ?? 'N/A';
        $avgReviewDays          = Application::whereIn('status', ['approved', 'rejected'])
            ->avg(DB::raw('DATEDIFF(updated_at, created_at)')) ?? 0;
        $avgReviewTime          = round($avgReviewDays, 1) . ' days';
        $newApproved            = Application::where('status', 'approved')
            ->whereMonth('updated_at', now()->month)
            ->count();
        $avgOnboardingDays      = Healthworker::whereNotNull('verified_at')
            ->avg(DB::raw('DATEDIFF(verified_at, created_at)')) ?? 0;
        $avgOnboardingTime      = round($avgOnboardingDays, 1) . ' days';
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
     * Approve or reject an application.
     */
    public function process(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:applications,id',
            'action' => 'required|in:approve,reject',
        ]);

        $application = Application::findOrFail($request->id);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Prevent duplicate users
                if (User::where('email', $application->email)->exists()) {
                    throw new \Exception('Email already exists');
                }

                // Create new user with random password
                $plainPassword = Str::random(12);
                $user = User::create([
                    'name'              => "{$application->first_name} {$application->last_name}",
                    'email'             => $application->email,
                    'usertype'          => 'healthworker',
                    'password'          => bcrypt($plainPassword),
                    'email_verified_at' => now(),
                ]);

                // Prepare document URLs for PDF snapshot
                $resumeUrl    = $application->resume         ? Storage::url($application->resume)           : null;
                $licenseUrl   = $application->license_doc    ? Storage::url($application->license_doc)      : null;
                $certsUrl     = $application->certifications ? Storage::url($application->certifications)   : null;

                // Generate PDF snapshot (won't abort on malformed UTF-8 in other fields)
                try {
                    $pdf = Pdf::loadView('admin.application_snapshot', [
                        'application' => $application,
                        'user'        => $user,
                        'resumeUrl'   => $resumeUrl,
                        'licenseUrl'  => $licenseUrl,
                        'certsUrl'    => $certsUrl,
                    ]);
                    $pdfPath = "snapshots/application_{$application->reference_number}.pdf";
                    Storage::put($pdfPath, $pdf->output());
                } catch (\Throwable $e) {
                    Log::warning("Snapshot PDF generation failed for Application ID {$application->id}: {$e->getMessage()}");
                    $pdfPath = null;
                }

                // Create Healthworker record
                Healthworker::create(array_filter([
                    'user_id'                  => $user->id,
                    'application_id'           => $application->id,
                    'name'                     => "{$application->first_name} {$application->last_name}",
                    'specialty'                => $application->specialization,
                    'resume'                   => $application->resume,
                    'license_doc'              => $application->license_doc,
                    'certifications'           => $application->certifications,
                    'application_snapshot_pdf' => $pdfPath,
                    'status'                   => 'active',
                    'verified_at'              => now(),
                ]));

                // Mark application as approved
                $application->update(['status' => 'approved']);

                DB::commit();

                $response = [
                    'message'  => 'Application approved successfully.',
                    'email'    => $user->email,
                    'password' => $plainPassword,
                ];

                if ($pdfPath) {
                    $url                          = Storage::url($pdfPath);
                    $response['snapshot_url']     = $url;
                    $response['pdf_url']          = $url;
                }

                return response()->json($response);
            }

            // Reject: soft-delete the application
            $application->delete();
            DB::commit();

            return response()->json(['message' => 'Application rejected successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process application ID {$application->id}: {$e->getMessage()}");
            return response()->json([
                'message' => 'Failed to process application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate a healthworker’s password by reference number.
     */
    public function regeneratePassword(Request $request, $reference_number)
    {
        $application = Application::where('reference_number', $reference_number)->firstOrFail();
        $user        = User::where('email', $application->email)->firstOrFail();

        DB::beginTransaction();
        try {
            $plainPassword = Str::random(12);
            $user->update(['password' => bcrypt($plainPassword)]);
            DB::commit();

            return response()->json([
                'message'  => 'Password regenerated successfully.',
                'password' => $plainPassword,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to regenerate password for Application Ref {$reference_number}: {$e->getMessage()}");
            return response()->json([
                'message' => 'Failed to regenerate password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a stored BLOB (resume, license_doc, certifications) as PDF.
     */
    public function download($application, $type)
    {
        if (! $application instanceof Application) {
            $application = Application::findOrFail($application);
        }

        $columns = [
            'resume'         => 'resume',
            'license_doc'    => 'license_doc',
            'certifications' => 'certifications',
        ];

        if (! isset($columns[$type])) {
            abort(404);
        }

        $content = $application->{$columns[$type]};
        if (! $content) {
            return back()->with('error', ucfirst(str_replace('_', ' ', $type)) . ' not available.');
        }

        $filename = "{$type}_{$application->reference_number}.pdf";

        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
