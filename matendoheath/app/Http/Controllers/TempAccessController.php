<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\Document;

class TempAccessController extends Controller
{
    // Handle the temporary access view
    public function view(Request $request, $id)
    {
        // Verify the user exists
        $user = User::findOrFail($id);

        // Fetch medical records for the user
        $records = MedicalRecord::where('user_id', $id)->latest()->get();

        // Fetch documents for the user
        $documents = Document::where('user_id', $id)->latest()->get();

        return view('temp-dashboard', compact('user', 'records', 'documents'));
    }

    // Generate a temporary signed URL for the user's profile
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
}

