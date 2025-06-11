<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Healthworker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class ApplicationController extends Controller
{
        /**
     * Display a listing of all applications.
     *
     * Fetches paginated applications and stats for the admin dashboard.
     */
    public function index()
    {
        $applications = Application::orderBy('created_at', 'desc')->paginate(10);
        $pending_applications = Application::where('status', 'pending')->count();
        $approved_applications = Application::where('status', 'approved')->count();
        $rejected_applications = Application::where('status', 'rejected')->count();

        return view('admin.partials._applications', compact(
            'applications',
            'pending_applications',
            'approved_applications',
            'rejected_applications'
        ));
    }

    /**
     * Download the requested document securely.
     *
     * Handles downloading of resume, license_doc, or certifications for an application.
     *
     * @param int $application The ID of the application.
     * @param string $type The type of file (resume, license_doc, certifications).
     */
    public function download($application, $type)
    {
        $application = Application::findOrFail($application);
        $allowed = config('files.allowed_application_files', ['resume', 'license_doc', 'certifications']);

        if (!in_array($type, $allowed)) {
            abort(404, 'Invalid document requested.');
        }

        $filePath = $application->{$type};

        if (!$filePath || !Str::startsWith($filePath, 'applications/') || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Process approval or rejection of an application.
     *
     * Approves or rejects an application, creating a user and healthworker record on approval,
     * generating a PDF snapshot, and sending credentials via email.
     *
     * @param Request $request The incoming request with application_id and action.
     */
    public function process(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'action' => 'required|in:approve,reject',
        ]);

        $application = Application::findOrFail($request->application_id);

        if ($application->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be processed.');
        }

        return DB::transaction(function () use ($request, $application) {
            try {
                if ($request->action === 'approve') {
                    $password = Str::random(12);
                    Log::info('Generated password for application ' . $application->id);

                    $user = User::create([
                        'name' => trim($application->first_name . ' ' . $application->last_name),
                        'email' => $application->email,
                        'usertype' => 'healthworker',
                        'password' => Hash::make($password),
                    ]);

                    if (!$user) {
                        Log::error('Failed to create user for application ' . $application->id);
                        return back()->with('error', 'Failed to create user account.');
                    }

                    $pdf = Pdf::loadView('admin.partials._snapshot', [
                        'application' => $application,
                        'status' => 'approved',
                    ]);
                    $pdfFilename = 'application_snapshot_' . $application->id . '_' . time() . '.pdf';
                    $pdfPath = 'healthworkers/snapshots/' . $pdfFilename;
                    Storage::disk('public')->put($pdfPath, $pdf->output());

                    $healthworker = Healthworker::create([
                        'user_id' => $user->id,
                        'application_id' => $application->id,
                        'name' => $user->name,
                        'specialty' => $application->specialization ?? $application->profession,
                        'resume' => $application->resume,
                        'license_doc' => $application->license_doc,
                        'certifications' => $application->certifications,
                        'application_snapshot_pdf' => $pdfPath,
                        'status' => 'active',
                    ]);

                    $application->update([
                        'status' => 'approved',
                        'confirmed' => true,
                        'user_id' => $user->id,
                    ]);

                    try {
                        Mail::send('admin.partials._credentials', [
                            'name' => $user->name,
                            'email' => $application->email,
                            'password' => $password,
                        ], function ($message) use ($application) {
                            $message->to($application->email)->subject('Your Account Credentials');
                        });
                        Log::info('Credentials email sent to ' . $application->email);
                    } catch (\Exception $e) {
                        Log::error('Failed to send credentials email for application ' . $application->id . ': ' . $e->getMessage());
                        return back()->with('warning', 'Application approved, but failed to send email.');
                    }

                    Log::info('Credentials for application ' . $application->id . ': ', [
                        'email' => $application->email,
                        'pdf' => $pdfPath,
                    ]);

                    session()->put("created_user_{$application->id}", [
                        'email' => $application->email,
                        'password' => $password,
                        'pdf' => $pdfPath,
                    ]);

                    return view('admin.partials._credentials', [
                        'application' => $application,
                        'success' => "Application for {$application->first_name} {$application->last_name} approved successfully. Credentials emailed to the user."
                    ]);
                } elseif ($request->action === 'reject') {
                    $application->update(['status' => 'rejected']);
                    $application->delete();
                    return redirect()->route('admin.applications.index')
                        ->with('success', "Application for {$application->first_name} {$application->last_name} rejected and deleted successfully.");
                }
            } catch (\Exception $e) {
                Log::error('Error processing application ' . $application->id . ': ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Regenerate password for a healthworker.
     *
     * Generates a new password, updates the user, and sends new credentials via email.
     *
     * @param Request $request
     * @param int $application The ID of the application.
     */
    public function regeneratePassword(Request $request, $application)
    {
        $key = 'regenerate-password:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['error' => 'Too many password regeneration attempts. Try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $application = Application::findOrFail($application);
        if ($application->status !== 'approved') {
            return response()->json(['error' => 'Only approved applications can regenerate passwords.'], 403);
        }

        $user = User::where('email', $application->email)->first();

        if (!$user) {
            Log::error('User not found for application ' . $application->id);
            return response()->json(['error' => 'User not found for this application.'], 404);
        }

        return DB::transaction(function () use ($application, $user) {
            try {
                $newPassword = Str::random(12);
                Log::info('Regenerated password for user ' . $user->id);

                $user->update(['password' => Hash::make($newPassword)]);

                $healthworker = Healthworker::where('application_id', $application->id)->first();
                $pdfPath = $healthworker->application_snapshot_pdf ?? null;

                if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
                    $pdf = Pdf::loadView('admin.partials._snapshot', [
                        'application' => $application,
                        'status' => 'approved'
                    ]);
                    $pdfFilename = 'application_snapshot_' . $application->id . '_' . time() . '.pdf';
                    $pdfPath = 'healthworkers/snapshots/' . $pdfFilename;
                    Storage::disk('public')->put($pdfPath, $pdf->output());
                    $healthworker->update(['application_snapshot_pdf' => $pdfPath]);
                }

                try {
                    Mail::send('admin.partials._credentials', [
                        'name' => $user->name,
                        'email' => $application->email,
                        'password' => $newPassword,
                    ], function ($message) use ($application) {
                        $message->to($application->email)->subject('Your New Account Credentials');
                    });
                    Log::info('New credentials email sent to ' . $application->email);
                } catch (\Exception $e) {
                    Log::error('Failed to send new credentials email for application ' . $application->id . ': ' . $e->getMessage());
                    return response()->json(['warning' => 'Password regenerated, but failed to send email.'], 200);
                }

                session()->put("created_user_{$application->id}", [
                    'email' => $application->email,
                    'password' => $newPassword,
                    'pdf' => $pdfPath,
                ]);

                return response()->json(['success' => 'Password regenerated successfully.', 'newPassword' => $newPassword]);
            } catch (\Exception $e) {
                Log::error('Error regenerating password for application ' . $application->id . ': ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Redownload the application snapshot PDF.
     *
     * Downloads the existing PDF snapshot or regenerates it if missing.
     *
     * @param int $application The ID of the application.
     */
    public function redownloadPdf($application)
    {
        $key = 'redownload-pdf:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()->with('error', 'Too many PDF download attempts. Try again later.');
        }
        RateLimiter::hit($key, 3600);

        $application = Application::findOrFail($application);
        if ($application->status !== 'approved') {
            return back()->with('error', 'Only approved applications can download PDFs.');
        }

        $healthworker = Healthworker::where('application_id', $application->id)->first();

        if (!$healthworker) {
            return back()->with('error', 'Healthworker record not found.');
        }

        $pdfPath = $healthworker->application_snapshot_pdf ?? null;

        if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
            return Storage::disk('public')->download($pdfPath);
        }

        return DB::transaction(function () use ($application, $healthworker) {
            try {
                $pdf = Pdf::loadView('admin.partials._snapshot', [
                    'application' => $application,
                    'status' => 'approved'
                ]);
                $pdfFilename = 'application_snapshot_' . $application->id . '_' . time() . '.pdf';
                $pdfPath = 'healthworkers/snapshots/' . $pdfFilename;
                Storage::disk('public')->put($pdfPath, $pdf->output());

                $healthworker->update(['application_snapshot_pdf' => $pdfPath]);

                return Storage::disk('public')->download($pdfPath);
            } catch (\Exception $e) {
                Log::error('Error redownloading PDF for application ' . $application->id . ': ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Display the application snapshot PDF in a modal.
     *
     * Streams the PDF for the given approved application.
     *
     * @param int $application The ID of the application.
     */
    public function snapshotPdf($application)
    {
        $key = 'snapshot-pdf:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['error' => 'Too many PDF view attempts. Try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $application = Application::findOrFail($application);
        if ($application->status !== 'approved') {
            return response()->json(['error' => 'Only approved applications can view PDFs.'], 403);
        }

        $healthworker = Healthworker::where('application_id', $application->id)->first();

        if (!$healthworker) {
            Log::error('Healthworker not found for application ' . $application->id);
            return response()->json(['error' => 'Healthworker record not found.'], 404);
        }

        return DB::transaction(function () use ($application, $healthworker) {
            try {
                $pdfPath = $healthworker->application_snapshot_pdf ?? null;

                if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                    return response()->file(Storage::disk('public')->path($pdfPath), [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . basename($pdfPath) . '"'
                    ]);
                }

                $pdf = Pdf::loadView('admin.partials._snapshot', [
                    'application' => $application,
                    'status' => 'approved'
                ]);
                $pdfFilename = 'application_snapshot_' . $application->id . '_' . time() . '.pdf';
                $pdfPath = 'healthworkers/snapshots/' . $pdfFilename;
                Storage::disk('public')->put($pdfPath, $pdf->output());

                $healthworker->update(['application_snapshot_pdf' => $pdfPath]);

                return response()->file(Storage::disk('public')->path($pdfPath), [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($pdfPath) . '"'
                ]);
            } catch (\Exception $e) {
                Log::error('Error generating PDF for application ' . $application->id . ': ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Show credentials pop-up for an approved application.
     *
     * Displays the credentials pop-up for a single approved application.
     *
     * @param int $application The ID of the application.
     */
    public function showCredentials($application)
    {
        $application = Application::findOrFail($application);
        if ($application->status !== 'approved') {
            return redirect()->route('admin.applications.index')->with('error', 'Only approved applications have credentials.');
        }

        return view('admin.partials._credentials', compact('application'));
    }

    /**
     * Resend credentials for an application.
     *
     * Resends the credentials email to the user.
     *
     * @param Request $request
     * @param int $application The ID of the application.
     */
    public function resendCredentials(Request $request, $application)
    {
        $key = 'resend-credentials:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['error' => 'Too many credential resend attempts. Try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $application = Application::findOrFail($application);
        if ($application->status !== 'approved') {
            return response()->json(['error' => 'Only approved applications can resend credentials.'], 403);
        }

        $credentials = session("created_user_{$application->id}");

        if (!$credentials) {
            return response()->json(['error' => 'No credentials found to resend.'], 404);
        }

        try {
            Mail::send('admin.partials._credentials', [
                'name' => $application->first_name . ' ' . $application->last_name,
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ], function ($message) use ($application) {
                $message->to($application->email)->subject('Your Account Credentials (Resent)');
            });
            Log::info('Credentials resent to ' . $application->email);
            return response()->json(['success' => 'Credentials resent successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to resend credentials for application ' . $application->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to resend credentials.'], 500);
        }
    }

    /**
     * Bulk process applications.
     *
     * Processes multiple applications (approve/reject) in one request.
     *
     * @param Request $request The incoming request with application_ids and action.
     */
    public function bulkProcess(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'action' => 'required|in:approve,reject',
        ]);

        $successCount = 0;
        $failedCount = 0;

        return DB::transaction(function () use ($request, &$successCount, &$failedCount) {
            foreach ($request->application_ids as $applicationId) {
                try {
                    $application = Application::findOrFail($applicationId);
                    if ($application->status !== 'pending') {
                        continue;
                    }

                    if ($request->action === 'approve') {
                        $password = Str::random(12);
                        $user = User::create([
                            'name' => trim($application->first_name . ' ' . $application->last_name),
                            'email' => $application->email,
                            'usertype' => 'healthworker',
                            'password' => Hash::make($password),
                        ]);

                        $pdf = Pdf::loadView('admin.partials._snapshot', [
                            'application' => $application,
                            'status' => 'approved',
                        ]);
                        $pdfFilename = 'application_snapshot_' . $application->id . '_' . time() . '.pdf';
                        $pdfPath = 'healthworkers/snapshots/' . $pdfFilename;
                        Storage::disk('public')->put($pdfPath, $pdf->output());

                        $healthworker = Healthworker::create([
                            'user_id' => $user->id,
                            'application_id' => $application->id,
                            'name' => $user->name,
                            'specialty' => $application->specialization ?? $application->profession,
                            'resume' => $application->resume,
                            'license_doc' => $application->license_doc,
                            'certifications' => $application->certifications,
                            'application_snapshot_pdf' => $pdfPath,
                            'status' => 'active',
                        ]);

                        $application->update([
                            'status' => 'approved',
                            'confirmed' => true,
                            'user_id' => $user->id,
                        ]);

                        try {
                            Mail::send('admin.partials._credentials', [
                                'name' => $user->name,
                                'email' => $application->email,
                                'password' => $password,
                            ], function ($message) use ($application) {
                                $message->to($application->email)->subject('Your Account Credentials');
                            });
                            Log::info('Credentials email sent to ' . $application->email);
                        } catch (\Exception $e) {
                            Log::error('Failed to send credentials email for application ' . $application->id . ': ' . $e->getMessage());
                        }

                        session()->put("created_user_{$application->id}", [
                            'email' => $application->email,
                            'password' => $password,
                            'pdf' => $pdfPath,
                        ]);
                    } elseif ($request->action === 'reject') {
                        $application->update(['status' => 'rejected']);
                        $application->delete();
                    }
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error('Error processing application ' . $applicationId . ': ' . $e->getMessage());
                    $failedCount++;
                }
            }

            $message = "Processed $successCount applications successfully.";
            if ($failedCount > 0) {
                $message .= " $failedCount applications failed to process.";
            }
            return redirect()->route('admin.applications.index')->with('success', $message);
        });
    }

    /**
     * Get credentials for an application.
     *
     * Returns the credentials stored in the session for the given application.
     *
     * @param int $application The ID of the application.
     */
    public function getCredentials($application)
    {
        $key = 'get-credentials:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['error' => 'Too many credential retrieval attempts. Try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $credentials = session("created_user_{$application}");

        if (!$credentials) {
            return response()->json(['error' => 'No credentials found for this application.'], 404);
        }

        return response()->json([
            'email' => $credentials['email'],
            'pdf' => $credentials['pdf'],
        ]);
    }
}