<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            Log::channel('security')->warning('Unauthenticated admin access attempt', [
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'admin') {
            Log::channel('security')->warning('Non-admin user attempted admin access', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
            ]);
            
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Log admin access for audit purposes
        Log::channel('audit')->info('Admin access', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'url' => $request->url(),
            'route' => $request->route() ? $request->route()->getName() : null,
        ]);

        return $next($request);
    }
}
