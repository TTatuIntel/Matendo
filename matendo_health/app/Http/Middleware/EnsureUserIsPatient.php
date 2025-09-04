<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPatient
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            Log::channel('security')->warning('Unauthenticated patient access attempt', [
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'patient') {
            Log::channel('security')->warning('Non-patient user attempted patient access', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
            ]);
            
            abort(403, 'Access denied. Patient account required.');
        }

        // Log patient access for medical compliance
        Log::channel('medical')->info('Patient access', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'url' => $request->url(),
            'route' => $request->route() ? $request->route()->getName() : null,
        ]);

        return $next($request);
    }
}
