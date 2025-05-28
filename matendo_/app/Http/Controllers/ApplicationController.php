<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\HealthWorker;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
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

        $application->status = 'approved';
        $application->save();

        // Generate application PDF snapshot
        $pdf = Pdf::loadView('pdf.application_summary', ['application' => $application]);
        $pdfPath = 'snapshots/application_' . $application->id . '_summary.pdf';
        Storage::put("public/{$pdfPath}", $pdf->output());

        // Create Health Worker from approved application
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

        return redirect()->back()->with('success', 'Application approved and Health Worker created.');
    }

    public function reject($id)
    {
        $application = Application::findOrFail($id);
        $application->status = 'rejected';
        $application->save();

        return redirect()->back()->with('warning', 'Application has been rejected.');
    }

public function updateStatus(Request $request, Application $application)
{
    $request->validate([
        'status' => 'required|in:approved,rejected',
    ]);

    $application->status = $request->input('status');
    $application->save();

    return redirect()->route('applications.index')->with('success', 'Application status updated successfully.');
}
}
