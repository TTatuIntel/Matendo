<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Approved;
use App\Models\HealthWorker;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::latest()->get();
        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        return view('admin.applications.show', compact('application'));
    }

    public function approve($id)
    {
        $application = Application::findOrFail($id);

        if ($application->status === 'approved') {
            return redirect()->back()->with('info', 'Application is already approved.');
        }

        try {
            DB::transaction(function () use ($application) {
                $application->status = 'approved';
                $application->save();

                // Store in the Approved table
                Approved::create([
                    'application_id' => $application->id,
                    'reference_code' => $application->reference_code,
                    'first_name' => $application->first_name,
                    'last_name' => $application->last_name,
                    'profession' => $application->profession,
                    'approved_at' => now(),
                ]);
            });

            return redirect()->back()->with('success', 'Application approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }

        // Commented-out PDF and HealthWorker logic for reference
        /*
        $pdf = Pdf::loadView('pdf.application_summary', ['application' => $application]);
        $pdfPath = 'snapshots/application_' . $application->id . '_summary.pdf';
        Storage::put("public/{$pdfPath}", $pdf->output());

        HealthWorker::create([
            'user_id' => $application->user_id,
            'application_id' => $application->id,
            'name' => $application->full_name,
            'specialty' => $application->specialty,
            'resume' => $application->resume,
            'license_doc' => $application->license_doc,
            'certifications' => $application->certifications,
            'application_snapshot_pdf' => "storage/{$pdfPath}",
        ]);
        */
    }

    public function reject($id)
    {
        $application = Application::findOrFail($id);
        $application->status = 'rejected';
        $application->save();

        return redirect()->back()->with('warning', 'Application has been rejected.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $application = Application::findOrFail($id);
                $application->status = $request->status;
                $application->save();

                if ($request->status === 'approved') {
                    Approved::create([
                        'application_id' => $application->id,
                        'reference_code' => $application->reference_code,
                        'first_name' => $application->first_name,
                        'last_name' => $application->last_name,
                        'profession' => $application->profession,
                        'approved_at' => now(),
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application status: ' . $e->getMessage()
            ], 500);
        }
    }
}
