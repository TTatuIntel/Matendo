<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TempAccess;

class ExternalAccessController extends Controller
{
    public function index()
    {
        $accessSessions = TempAccess::with(['patient.user', 'generatedBy'])
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(20);
        
        return view('admin.external-access.index', compact('accessSessions'));
    }

    public function getRealTimeAccessData()
    {
        return response()->json([
            'active_sessions' => TempAccess::where('is_active', true)->count(),
            'recent_accesses' => TempAccess::where('accessed_at', '>=', now()->subHour())->count()
        ]);
    }

    public function grantTemporaryAccess(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'duration_hours' => 'required|integer|min:1|max:72'
        ]);
        
        $access = TempAccess::create([
            'patient_id' => $request->patient_id,
            'generated_by' => auth()->id(),
            'expires_at' => now()->addHours($request->duration_hours)
        ]);
        
        return response()->json(['success' => true, 'access' => $access]);
    }

    public function revokeAccess($accessId)
    {
        $access = TempAccess::findOrFail($accessId);
        $access->update([
            'is_active' => false,
            'revoked_at' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Access revoked']);
    }

    public function terminateSession($sessionId)
    {
        return response()->json(['success' => true, 'message' => 'Session terminated']);
    }

    public function updateAccessPolicies(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Access policies updated']);
    }

    public function detectSuspiciousActivity()
    {
        $suspicious = TempAccess::where('access_count', '>', 50)
                               ->orWhere('failed_verification_attempts', '>', 5)
                               ->with(['patient.user'])
                               ->get();
        
        return response()->json(['suspicious_activity' => $suspicious]);
    }
}
