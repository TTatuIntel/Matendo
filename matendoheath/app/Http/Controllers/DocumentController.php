<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function view(Document $document)
    {
        // Verify access
        if (auth()->id() !== $document->user_id &&
            (!session()->has('temp_access_user_id') ||
            session('temp_access_user_id') !== $document->user_id) {
            abort(403);
        }

        return Storage::response($document->path, $document->filename, [
            'Content-Type' => $document->mime_type,
        ]);
    }

    public function download(Document $document)
    {
        // Verify access
        if (auth()->id() !== $document->user_id &&
            (!session()->has('temp_access_user_id') ||
            session('temp_access_user_id') !== $document->user_id) {
            abort(403);
        }

        return Storage::download($document->path, $document->filename);
    }
}
