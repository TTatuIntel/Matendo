<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HealthRecordController extends Controller
{
    public function display()
    {
        $records = MedicalRecord::where('user_id', Auth::id())->latest()->get();
        return view('display', compact('records'));
    }

    public function showUploadForm()
    {
        $documents = Document::where('user_id', Auth::id())->latest()->get();
        return view('upload', compact('documents'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'category' => 'required|string',
    //         'data' => 'required|array',
    //     ]);

    //     $data = $request->input('data');
    //     foreach ($data as $key => $value) {
    //         if (is_null($value) || $value === '') {
    //             unset($data[$key]);
    //         }
    //     }

    //     MedicalRecord::create([
    //         'user_id' => Auth::id(),
    //         'category' => $request->category,
    //         'data' => $data,
    //     ]);

    //     return redirect()->route('display')->with('success', 'Record added successfully!');
    // }
public function store(Request $request)
{
    $request->validate([
        'category' => 'required|string',
        'data' => 'required|array',
    ]);

    $data = $request->input('data');
    foreach ($data as $key => $value) {
        if (is_null($value) || $value === '') {
            unset($data[$key]);
        }
    }

    $record = MedicalRecord::create([
        'user_id' => Auth::id(),
        'category' => $request->category,
        'data' => $data,
    ]);

    // Return JSON response for AJAX requests
    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Record added successfully!',
            'data' => $record
        ]);
    }

    return redirect()->route('display')->with('success', 'Record added successfully!');
}

    public function destroy($id)
    {
        $record = MedicalRecord::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $record->delete();
        return redirect()->route('display')->with('success', 'Record deleted successfully!');
    }

    public function uploadDocuments(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB
            'category' => 'nullable|string|in:lab_results,prescriptions,medical_reports',
        ]);

        $files = $request->file('files');
        $category = $request->input('category', null);
        $uploadedFiles = [];

        foreach ($files as $file) {
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads', $filename); // Remove 'public/' from the path
            $size = $file->getSize();

            $document = Document::create([
                'user_id' => Auth::id(),
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $size,
                'category' => $category,
            ]);

            $uploadedFiles[] = [
                'id' => $document->id,
                'filename' => $document->filename,
                'size' => $size,
                'category' => $category,
                'created_at' => $document->created_at->toDateTimeString(),
                'url' => Storage::url($path),
            ];
        }

        return response()->json(['success' => true, 'files' => $uploadedFiles]);
    }

    public function downloadDocument($id)
    {
        $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return Storage::download($document->path, $document->filename);
    }

public function viewDocument($id)
{
    $document = Document::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

    // Check if file exists
    if (!Storage::exists($document->path)) {
        abort(404);
    }

    // Get the file's MIME type
    $mimeType = Storage::mimeType($document->path);

    // Create response with appropriate headers
    $headers = [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="'.$document->filename.'"'
    ];

    return response()->file(storage_path('app/'.$document->path), $headers);
}
}
