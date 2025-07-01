<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TempAccessController extends Controller
{
    // public function view(Request $request, $id)
    // {
    //     // Verify signed URL
    //     if (!$request->hasValidSignature()) {
    //         abort(403, 'Invalid or expired link');
    //     }

    //     // Set temporary access session
    //     session(['temp_access_user_id' => $id]);
    //     session(['temp_access_expires_at' => now()->addMinutes(55)]); // Slightly less than URL expiry

    //     $user = User::findOrFail($id);
    //     $records = MedicalRecord::where('user_id', $id)->latest()->get();
    //     $documents = Document::where('user_id', $id)->latest()->get();

    //     return view('temp-dashboard', compact('user', 'records', 'documents'));
    // }

 public function view(Request $request, $id)
    {
        // Verify signed URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired link');
        }

        // Set temporary access session
        session(['temp_access_user_id' => $id]);
        session(['temp_access_expires_at' => now()->addMinutes(55)]); // Slightly less than URL expiry

        $user = User::findOrFail($id);
        $records = MedicalRecord::where('user_id', $id)->latest()->get();
        $documents = Document::where('user_id', $id)->latest()->get();

        return view('temp-dashboard', compact('user', 'records', 'documents'));
    }

    // public function generateLink(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);

    //     $url = URL::temporarySignedRoute(
    //         'temp.access',
    //         now()->addMinutes(60), // expires in 1 hour
    //         ['id' => $user->id]
    //     );

    //     return back()->with('temp_link', $url);
    // }

public function generateLink(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $url = URL::temporarySignedRoute(
            'temp.access',
            now()->addMinutes(60), // expires in 1 hour
            ['id' => $user->id]
        );

        return back()->with('temp_link', $url);
    }

    // public function tempUpload(Request $request)
    // {
    //     // Verify temporary access
    //     if (!session()->has('temp_access_user_id') ||
    //         now()->gt(session('temp_access_expires_at'))) {
    //         return response()->json(['error' => 'Unauthorized or session expired'], 401);
    //     }

    //     $userId = session('temp_access_user_id');
    //     $category = $request->input('category', null);

    //     $validated = $request->validate([
    //         'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
    //     ]);

    //     $uploadedFiles = [];

    //     foreach ($request->file('files') as $file) {
    //         $originalName = $file->getClientOriginalName();
    //         $extension = $file->getClientOriginalExtension();
    //         $fileName = Str::random(20) . '.' . $extension;
    //         $filePath = 'documents/' . $fileName;

    //         // Store file
    //         Storage::put($filePath, file_get_contents($file));

    //         // Create document record
    //         $document = Document::create([
    //             'user_id' => $userId,
    //             'filename' => $originalName,
    //             'path' => $filePath,
    //             'size' => $file->getSize(),
    //             'category' => $category,
    //             'mime_type' => $file->getMimeType(),
    //         ]);

    //         $uploadedFiles[] = $document;
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'files' => $uploadedFiles,
    //         'message' => count($uploadedFiles) . ' file(s) uploaded successfully'
    //     ]);
    // }

// public function tempUpload(Request $request)
// {
//     // Verify temporary access
//     if (!session()->has('temp_access_user_id') ||
//         now()->gt(session('temp_access_expires_at'))) {
//         return response()->json(['error' => 'Unauthorized or session expired'], 401);
//     }

//     $userId = session('temp_access_user_id');

//     $validated = $request->validate([
//         'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
//         'uploader_name' => 'required|string|max:255',
//         'uploader_hospital' => 'required|string|max:255',
//         'category' => 'nullable|string'
//     ]);

//     $uploadedFiles = [];

//     foreach ($request->file('files') as $file) {
//         $originalName = $file->getClientOriginalName();
//         $extension = $file->getClientOriginalExtension();
//         $fileName = Str::random(20) . '.' . $extension;
//         $filePath = 'documents/' . $fileName;

//         // Store file
//         Storage::put($filePath, file_get_contents($file));

//         // Create document record with uploader info
//         $document = Document::create([
//             'user_id' => $userId,
//             'filename' => $originalName,
//             'path' => $filePath,
//             'size' => $file->getSize(),
//             'category' => $request->input('category'),
//             'mime_type' => $file->getMimeType(),
//             'uploader_name' => $request->input('uploader_name'),
//             'uploader_hospital' => $request->input('uploader_hospital')
//         ]);

//         $uploadedFiles[] = $document;
//     }

//     return response()->json([
//         'success' => true,
//         'files' => $uploadedFiles,
//         'message' => count($uploadedFiles) . ' file(s) uploaded successfully'
//     ]);
// }


public function tempUpload(Request $request)
{
    \Log::debug('Upload request data:', $request->all());

    // Verify temporary access
    if (!session()->has('temp_access_user_id') ||
        now()->gt(session('temp_access_expires_at'))) {
        return response()->json(['error' => 'Unauthorized or session expired'], 401);
    }

    $userId = session('temp_access_user_id');

    $validated = $request->validate([
        'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        'uploader_name' => 'required|string|max:255',
        'uploader_hospital' => 'required|string|max:255',
        'category' => 'nullable|string'
    ]);

    \Log::debug('Validated data:', $validated);

    $uploadedFiles = [];

    foreach ($request->file('files') as $file) {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(20) . '.' . $extension;
        $filePath = 'documents/' . $fileName;

        Storage::put($filePath, file_get_contents($file));

        $document = Document::create([
            'user_id' => $userId,
            'filename' => $originalName,
            'path' => $filePath,
            'size' => $file->getSize(),
            'category' => $request->input('category'),
            'mime_type' => $file->getMimeType(),
            'uploader_name' => $request->input('uploader_name'), // Explicitly set
            'uploader_hospital' => $request->input('uploader_hospital') // Explicitly set
        ]);

        \Log::debug('Created document:', $document->toArray());

        $uploadedFiles[] = $document;
    }

    return response()->json([
        'success' => true,
        'files' => $uploadedFiles,
        'message' => count($uploadedFiles) . ' file(s) uploaded successfully'
    ]);
}




}
