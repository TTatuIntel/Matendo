<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use App\Models\User;

class AdminSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is admin
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access to admin area.');
        }

        // Check if user account is active
        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->withErrors(['error' => 'Account is inactive.']);
        }

        // Check for IP blocking
        if ($this->isIpBlocked($request->ip())) {
            abort(403, 'Your IP address has been blocked.');
        }

        // Check for suspicious activity
        if ($this->detectSuspiciousActivity($user, $request)) {
            $this->logSuspiciousActivity($user, $request);
            
            // Optionally block user or require additional authentication
            if ($this->shouldBlockSuspiciousUser($user, $request)) {
                $user->update(['status' => 'suspended']);
                auth()->logout();
                return redirect()->route('login')->withErrors(['error' => 'Account suspended due to suspicious activity.']);
            }
        }

        // Check for forced password reset
        if ($user->password_reset_required ?? false) {
            return redirect()->route('admin.profile.password-reset')
                          ->withErrors(['warning' => 'Password reset is required before continuing.']);
        }

        // Rate limiting for admin actions
        if ($this->isRateLimited($user, $request)) {
            return response()->json([
                'error' => 'Too many requests. Please slow down.',
                'retry_after' => 60
            ], 429);
        }

        // Log admin activity
        $this->logAdminActivity($user, $request);

        // Update last activity
        $user->update(['last_activity' => now()]);

        return $next($request);
    }

    /**
     * Check if IP is blocked
     */
    private function isIpBlocked(string $ip): bool
    {
        $blockedIps = Cache::get('blocked_ips', []);
        
        if (!isset($blockedIps[$ip])) {
            return false;
        }
        
        $blockInfo = $blockedIps[$ip];
        
        // Check if block has expired
        if (isset($blockInfo['expires_at']) && now()->gt($blockInfo['expires_at'])) {
            // Remove expired block
            unset($blockedIps[$ip]);
            Cache::put('blocked_ips', $blockedIps, now()->addYear());
            return false;
        }
        
        return true;
    }

    /**
     * Detect suspicious activity patterns
     */
    private function detectSuspiciousActivity(User $user, Request $request): bool
    {
        $suspiciousPatterns = [
            'rapid_requests' => $this->checkRapidRequests($user, $request),
            'unusual_hours' => $this->checkUnusualHours($user, $request),
            'new_ip_address' => $this->checkNewIpAddress($user, $request),
            'privilege_escalation' => $this->checkPrivilegeEscalation($user, $request),
            'mass_actions' => $this->checkMassActions($user, $request)
        ];

        return collect($suspiciousPatterns)->filter()->count() >= 2; // 2 or more patterns = suspicious
    }

    /**
     * Check for rapid requests
     */
    private function checkRapidRequests(User $user, Request $request): bool
    {
        $cacheKey = "admin_requests:{$user->id}";
        $requests = Cache::get($cacheKey, []);
        
        // Add current request
        $requests[] = now()->timestamp;
        
        // Keep only last 5 minutes
        $requests = array_filter($requests, function($timestamp) {
            return $timestamp > (now()->timestamp - 300);
        });
        
        Cache::put($cacheKey, $requests, now()->addMinutes(10));
        
        // Suspicious if more than 50 requests in 5 minutes
        return count($requests) > 50;
    }

    /**
     * Check for unusual hours access
     */
    private function checkUnusualHours(User $user, Request $request): bool
    {
        $currentHour = now()->hour;
        
        // Consider 2 AM to 6 AM as unusual hours
        return $currentHour >= 2 && $currentHour <= 6;
    }

    /**
     * Check for new IP address
     */
    private function checkNewIpAddress(User $user, Request $request): bool
    {
        $cacheKey = "user_ips:{$user->id}";
        $knownIps = Cache::get($cacheKey, []);
        
        if (!in_array($request->ip(), $knownIps)) {
            $knownIps[] = $request->ip();
            Cache::put($cacheKey, $knownIps, now()->addDays(30));
            return true;
        }
        
        return false;
    }

    /**
     * Check for privilege escalation attempts
     */
    private function checkPrivilegeEscalation(User $user, Request $request): bool
    {
        $route = $request->route()->getName();
        
        // Check for attempts to access super-admin functions
        $sensitiveRoutes = [
            'admin.users.store',
            'admin.users.destroy',
            'admin.system.maintenance',
            'admin.backup.restore',
            'admin.security.block-ip'
        ];
        
        return in_array($route, $sensitiveRoutes);
    }

    /**
     * Check for mass actions
     */
    private function checkMassActions(User $user, Request $request): bool
    {
        $input = $request->all();
        
        // Check for bulk operations
        if (isset($input['user_ids']) && is_array($input['user_ids'])) {
            return count($input['user_ids']) > 10; // More than 10 users at once
        }
        
        if (isset($input['patient_ids']) && is_array($input['patient_ids'])) {
            return count($input['patient_ids']) > 20; // More than 20 patients at once
        }
        
        return false;
    }

    /**
     * Log suspicious activity
     */
    private function logSuspiciousActivity(User $user, Request $request): void
    {
        ActivityLog::create([
            'log_name' => 'suspicious_activity',
            'description' => "Suspicious admin activity detected for user: {$user->name}",
            'causer_id' => $user->id,
            'properties' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
                'input_keys' => array_keys($request->except(['password', '_token'])),
                'timestamp' => now(),
                'session_id' => session()->getId()
            ]
        ]);
    }

    /**
     * Determine if suspicious user should be blocked
     */
    private function shouldBlockSuspiciousUser(User $user, Request $request): bool
    {
        $suspiciousCount = ActivityLog::where('causer_id', $user->id)
                                    ->where('log_name', 'suspicious_activity')
                                    ->where('created_at', '>=', now()->subHour())
                                    ->count();
        
        return $suspiciousCount >= 3; // 3 suspicious activities in 1 hour = block
    }

    /**
     * Check for rate limiting
     */
    private function isRateLimited(User $user, Request $request): bool
    {
        $cacheKey = "admin_rate_limit:{$user->id}";
        $attempts = Cache::get($cacheKey, 0);
        
        // Allow 100 requests per minute for admin users
        if ($attempts >= 100) {
            return true;
        }
        
        Cache::put($cacheKey, $attempts + 1, now()->addMinute());
        return false;
    }

    /**
     * Log admin activity
     */
    private function logAdminActivity(User $user, Request $request): void
    {
        // Only log important admin actions, not every page view
        $importantRoutes = [
            'admin.users.store',
            'admin.users.update',
            'admin.users.destroy',
            'admin.users.toggle-status',
            'admin.users.reset-password',
            'admin.system.clear-cache',
            'admin.system.maintenance',
            'admin.backup.create',
            'admin.backup.restore',
            'admin.security.block-ip',
            'admin.security.create-incident',
            'admin.settings.update'
        ];
        
        $route = $request->route()->getName();
        
        if (in_array($route, $importantRoutes)) {
            ActivityLog::create([
                'log_name' => 'admin_activity',
                'description' => "Admin action: {$route}",
                'causer_id' => $user->id,
                'properties' => [
                    'route' => $route,
                    'method' => $request->method(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now(),
                    'input_sanitized' => $this->sanitizeInputForLogging($request->all())
                ]
            ]);
        }
    }

    /**
     * Sanitize input data for logging
     */
    private function sanitizeInputForLogging(array $input): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'secret', '_token', 'api_key', 'access_token'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($input[$key])) {
                $input[$key] = '[REDACTED]';
            }
        }
        
        return $input;
    }
}
