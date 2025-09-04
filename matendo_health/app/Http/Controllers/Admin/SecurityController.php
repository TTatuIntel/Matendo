<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class SecurityController extends Controller
{
    /**
     * Display security dashboard
     */
    public function index()
    {
        try {
            $securityStats = [
                'active_sessions' => $this->getActiveSessionsCount(),
                'failed_login_attempts' => $this->getFailedLoginAttempts(),
                'blocked_ips' => $this->getBlockedIpsCount(),
                'security_incidents' => $this->getSecurityIncidentsCount(),
                'suspicious_activities' => $this->getSuspiciousActivitiesCount(),
                'two_factor_enabled_users' => $this->getTwoFactorEnabledCount(),
                'last_security_scan' => $this->getLastSecurityScan(),
                'system_vulnerabilities' => $this->getSystemVulnerabilities()
            ];

            $recentIncidents = $this->getRecentSecurityIncidents();
            $activeThreats = $this->getActiveThreats();
            $securityLogs = $this->getRecentSecurityLogs();

            return view('admin.security.index', compact(
                'securityStats', 
                'recentIncidents', 
                'activeThreats', 
                'securityLogs'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load security dashboard: ' . $e->getMessage()]);
        }
    }

    /**
     * Display security incidents
     */
    public function incidents(Request $request)
    {
        try {
            $query = $this->buildIncidentsQuery($request);
            
            $incidents = $query->orderBy('created_at', 'desc')
                             ->paginate(20);

            $incidentTypes = $this->getIncidentTypes();
            $severityLevels = ['low', 'medium', 'high', 'critical'];

            return view('admin.security.incidents', compact(
                'incidents', 
                'incidentTypes', 
                'severityLevels'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load security incidents: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a new security incident
     */
    public function createIncident(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'severity' => 'required|in:low,medium,high,critical',
                'incident_type' => 'required|string|max:100',
                'affected_user_id' => 'nullable|exists:users,id',
                'ip_address' => 'nullable|ip',
                'evidence' => 'nullable|array',
                'immediate_action_taken' => 'nullable|string|max:1000'
            ]);

            $incident = $this->createSecurityIncident([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'severity' => $validated['severity'],
                'incident_type' => $validated['incident_type'],
                'affected_user_id' => $validated['affected_user_id'] ?? null,
                'ip_address' => $validated['ip_address'] ?? request()->ip(),
                'evidence' => json_encode($validated['evidence'] ?? []),
                'immediate_action_taken' => $validated['immediate_action_taken'] ?? null,
                'reported_by' => auth()->id(),
                'status' => 'open',
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security incident created successfully',
                'incident_id' => $incident['id']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create security incident: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display active sessions management
     */
    public function sessions(Request $request)
    {
        try {
            $sessions = $this->getActiveSessions($request);
            $sessionStats = $this->getSessionStatistics();

            return view('admin.security.sessions', compact('sessions', 'sessionStats'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load sessions: ' . $e->getMessage()]);
        }
    }

    /**
     * Revoke a user session
     */
    public function revokeSession($sessionId, Request $request)
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:255'
            ]);

            // Get session details before revoking
            $session = $this->getSessionDetails($sessionId);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Revoke the session
            $this->terminateSession($sessionId);

            // Log session revocation
            ActivityLog::create([
                'log_name' => 'security',
                'description' => "Session revoked for user: {$session['user_name']}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'session_id' => $sessionId,
                    'revoked_user_id' => $session['user_id'],
                    'reason' => $validated['reason'] ?? 'Admin action',
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session revoked successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display failed login attempts
     */
    public function failedLogins(Request $request)
    {
        try {
            $failedLogins = $this->getFailedLoginAttempts($request);
            $suspiciousIPs = $this->getSuspiciousIPs();
            $blockedIPs = $this->getBlockedIPs();

            return view('admin.security.failed-logins', compact(
                'failedLogins', 
                'suspiciousIPs', 
                'blockedIPs'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load failed logins: ' . $e->getMessage()]);
        }
    }

    /**
     * Block an IP address
     */
    public function blockIp(Request $request)
    {
        try {
            $validated = $request->validate([
                'ip_address' => 'required|ip',
                'reason' => 'required|string|max:255',
                'duration' => 'nullable|integer|min:1|max:525600', // Max 1 year in minutes
                'block_type' => 'required|in:temporary,permanent'
            ]);

            $expiresAt = null;
            if ($validated['block_type'] === 'temporary' && isset($validated['duration'])) {
                $expiresAt = now()->addMinutes($validated['duration']);
            }

            $this->addBlockedIP([
                'ip_address' => $validated['ip_address'],
                'reason' => $validated['reason'],
                'blocked_by' => auth()->id(),
                'expires_at' => $expiresAt,
                'created_at' => now()
            ]);

            // Log IP blocking
            ActivityLog::create([
                'log_name' => 'security',
                'description' => "IP address blocked: {$validated['ip_address']}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'blocked_ip' => $validated['ip_address'],
                    'reason' => $validated['reason'],
                    'block_type' => $validated['block_type'],
                    'duration_minutes' => $validated['duration'] ?? null,
                    'expires_at' => $expiresAt,
                    'admin_ip' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'IP address blocked successfully',
                'expires_at' => $expiresAt ? $expiresAt->toISOString() : null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to block IP address: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unblock an IP address
     */
    public function unblockIp(Request $request)
    {
        try {
            $validated = $request->validate([
                'ip_address' => 'required|ip',
                'reason' => 'nullable|string|max:255'
            ]);

            $this->removeBlockedIP($validated['ip_address']);

            // Log IP unblocking
            ActivityLog::create([
                'log_name' => 'security',
                'description' => "IP address unblocked: {$validated['ip_address']}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'unblocked_ip' => $validated['ip_address'],
                    'reason' => $validated['reason'] ?? 'Admin action',
                    'admin_ip' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'IP address unblocked successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unblock IP address: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force password reset for a user
     */
    public function forcePasswordReset(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'reason' => 'required|string|max:255',
                'notify_user' => 'boolean'
            ]);

            $user = User::findOrFail($validated['user_id']);
            
            // Force password reset
            $user->update([
                'password_reset_required' => true,
                'password_reset_reason' => $validated['reason'],
                'password_reset_by' => auth()->id(),
                'password_reset_at' => now()
            ]);

            // Invalidate all user sessions
            $this->invalidateUserSessions($user->id);

            // Log forced password reset
            ActivityLog::create([
                'log_name' => 'security',
                'description' => "Forced password reset for user: {$user->name}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'affected_user_id' => $user->id,
                    'reason' => $validated['reason'],
                    'notify_user' => $validated['notify_user'] ?? false,
                    'admin_ip' => request()->ip()
                ]
            ]);

            // Send notification to user if requested
            if ($validated['notify_user'] ?? false) {
                // Send password reset notification
                $this->sendPasswordResetNotification($user, $validated['reason']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset forced successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to force password reset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run security scan
     */
    public function runSecurityScan(Request $request)
    {
        try {
            $scanResults = [
                'scan_id' => \Illuminate\Support\Str::uuid(),
                'started_at' => now(),
                'vulnerabilities' => [],
                'recommendations' => [],
                'threats_detected' => 0,
                'scan_duration' => 0
            ];

            $startTime = microtime(true);

            // Check for weak passwords
            $weakPasswords = $this->checkWeakPasswords();
            if ($weakPasswords > 0) {
                $scanResults['vulnerabilities'][] = [
                    'type' => 'weak_passwords',
                    'severity' => 'medium',
                    'description' => "{$weakPasswords} users have weak passwords",
                    'recommendation' => 'Enforce stronger password policies'
                ];
                $scanResults['threats_detected']++;
            }

            // Check for inactive admin accounts
            $inactiveAdmins = $this->checkInactiveAdminAccounts();
            if ($inactiveAdmins > 0) {
                $scanResults['vulnerabilities'][] = [
                    'type' => 'inactive_admin_accounts',
                    'severity' => 'high',
                    'description' => "{$inactiveAdmins} admin accounts haven't been used recently",
                    'recommendation' => 'Review and disable unused admin accounts'
                ];
                $scanResults['threats_detected']++;
            }

            // Check for suspicious login patterns
            $suspiciousLogins = $this->checkSuspiciousLoginPatterns();
            if ($suspiciousLogins > 0) {
                $scanResults['vulnerabilities'][] = [
                    'type' => 'suspicious_login_patterns',
                    'severity' => 'high',
                    'description' => "{$suspiciousLogins} suspicious login patterns detected",
                    'recommendation' => 'Investigate unusual login activities'
                ];
                $scanResults['threats_detected']++;
            }

            // Check system file permissions
            $filePermissionIssues = $this->checkFilePermissions();
            if ($filePermissionIssues > 0) {
                $scanResults['vulnerabilities'][] = [
                    'type' => 'file_permissions',
                    'severity' => 'medium',
                    'description' => "{$filePermissionIssues} files have incorrect permissions",
                    'recommendation' => 'Review and fix file permissions'
                ];
                $scanResults['threats_detected']++;
            }

            // Check for outdated dependencies (placeholder)
            $scanResults['vulnerabilities'][] = [
                'type' => 'outdated_dependencies',
                'severity' => 'low',
                'description' => 'Some dependencies may be outdated',
                'recommendation' => 'Run composer audit and update dependencies'
            ];

            $scanResults['scan_duration'] = round(microtime(true) - $startTime, 2);
            $scanResults['completed_at'] = now();

            // Cache scan results
            Cache::put('last_security_scan', $scanResults, now()->addHours(24));

            // Log security scan
            ActivityLog::create([
                'log_name' => 'security_scan',
                'description' => 'Security scan completed',
                'causer_id' => auth()->id(),
                'properties' => [
                    'scan_id' => $scanResults['scan_id'],
                    'threats_detected' => $scanResults['threats_detected'],
                    'scan_duration' => $scanResults['scan_duration'],
                    'admin_ip' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security scan completed',
                'scan_results' => $scanResults
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Security scan failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export security logs
     */
    public function exportSecurityLogs(Request $request)
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'log_type' => 'nullable|in:all,incidents,failed_logins,blocks,scans'
            ]);

            $logs = $this->getSecurityLogsForExport($validated);

            return response()->streamDownload(function () use ($logs) {
                echo "Security Logs Export\n";
                echo "Generated: " . now() . "\n\n";
                echo "Timestamp,Type,Description,User,IP Address,Severity\n";
                
                foreach ($logs as $log) {
                    echo implode(',', [
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->log_name ?? 'N/A',
                        '"' . ($log->description ?? 'No description') . '"',
                        $log->causer->name ?? 'System',
                        $log->properties['ip_address'] ?? 'N/A',
                        $log->properties['severity'] ?? 'N/A'
                    ]) . "\n";
                }
            }, 'security-logs-' . now()->format('Y-m-d') . '.csv');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export security logs: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods
    private function getActiveSessionsCount()
    {
        // This would query actual session storage
        return Cache::get('active_sessions_count', 0);
    }

    private function getFailedLoginAttempts($request = null)
    {
        return ActivityLog::where('log_name', 'auth')
                         ->where('description', 'like', '%failed login%')
                         ->where('created_at', '>=', now()->subHours(24))
                         ->count();
    }

    private function getBlockedIpsCount()
    {
        // This would query blocked IPs from cache or database
        return Cache::get('blocked_ips_count', 0);
    }

    private function getSecurityIncidentsCount()
    {
        return ActivityLog::where('log_name', 'security')
                         ->where('created_at', '>=', now()->subMonth())
                         ->count();
    }

    private function getSuspiciousActivitiesCount()
    {
        return ActivityLog::where('log_name', 'suspicious_activity')
                         ->where('created_at', '>=', now()->subWeek())
                         ->count();
    }

    private function getTwoFactorEnabledCount()
    {
        return User::where('two_factor_enabled', true)->count();
    }

    private function getLastSecurityScan()
    {
        $lastScan = Cache::get('last_security_scan');
        return $lastScan ? $lastScan['completed_at'] : 'Never';
    }

    private function getSystemVulnerabilities()
    {
        $lastScan = Cache::get('last_security_scan');
        return $lastScan ? $lastScan['threats_detected'] : 'Unknown';
    }

    private function getRecentSecurityIncidents()
    {
        return ActivityLog::where('log_name', 'security')
                         ->orderBy('created_at', 'desc')
                         ->limit(10)
                         ->get();
    }

    private function getActiveThreats()
    {
        // This would identify active threats
        return [];
    }

    private function getRecentSecurityLogs()
    {
        return ActivityLog::whereIn('log_name', ['security', 'auth', 'suspicious_activity'])
                         ->orderBy('created_at', 'desc')
                         ->limit(20)
                         ->get();
    }

    private function buildIncidentsQuery($request)
    {
        // This would build a proper query for security incidents
        return ActivityLog::where('log_name', 'security');
    }

    private function getIncidentTypes()
    {
        return ['unauthorized_access', 'data_breach', 'malicious_activity', 'policy_violation', 'system_compromise'];
    }

    private function createSecurityIncident($data)
    {
        // Create incident record
        ActivityLog::create([
            'log_name' => 'security_incident',
            'description' => $data['title'],
            'causer_id' => $data['reported_by'],
            'properties' => $data
        ]);

        return ['id' => \Illuminate\Support\Str::uuid()];
    }

    private function getActiveSessions($request)
    {
        // This would query active sessions from storage
        return collect([]);
    }

    private function getSessionStatistics()
    {
        return [
            'total_sessions' => 0,
            'active_sessions' => 0,
            'expired_sessions' => 0
        ];
    }

    private function getSessionDetails($sessionId)
    {
        // This would get session details from storage
        return null;
    }

    private function terminateSession($sessionId)
    {
        // This would terminate the session
        return true;
    }

    private function getSuspiciousIPs()
    {
        return [];
    }

    private function getBlockedIPs()
    {
        return Cache::get('blocked_ips', []);
    }

    private function addBlockedIP($data)
    {
        $blockedIPs = Cache::get('blocked_ips', []);
        $blockedIPs[$data['ip_address']] = $data;
        Cache::put('blocked_ips', $blockedIPs, now()->addYear());
    }

    private function removeBlockedIP($ipAddress)
    {
        $blockedIPs = Cache::get('blocked_ips', []);
        unset($blockedIPs[$ipAddress]);
        Cache::put('blocked_ips', $blockedIPs, now()->addYear());
    }

    private function invalidateUserSessions($userId)
    {
        // This would invalidate all sessions for a user
        return true;
    }

    private function sendPasswordResetNotification($user, $reason)
    {
        // This would send a password reset notification
        return true;
    }

    // Security scan helper methods
    private function checkWeakPasswords()
    {
        // Check for users with weak passwords (placeholder)
        return User::where('password_strength', '<', 3)->count();
    }

    private function checkInactiveAdminAccounts()
    {
        return User::where('role', 'admin')
                  ->where('last_activity', '<', now()->subMonth())
                  ->count();
    }

    private function checkSuspiciousLoginPatterns()
    {
        // Check for suspicious login patterns (placeholder)
        return ActivityLog::where('log_name', 'auth')
                         ->where('created_at', '>=', now()->subWeek())
                         ->whereRaw('JSON_EXTRACT(properties, "$.suspicious") = true')
                         ->count();
    }

    private function checkFilePermissions()
    {
        $issues = 0;
        $criticalFiles = [
            base_path('.env'),
            storage_path(),
            config_path()
        ];

        foreach ($criticalFiles as $file) {
            if (File::exists($file) && (fileperms($file) & 0777) > 0644) {
                $issues++;
            }
        }

        return $issues;
    }

    private function getSecurityLogsForExport($filters)
    {
        $query = ActivityLog::whereIn('log_name', ['security', 'auth', 'security_incident']);

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        if (!empty($filters['log_type']) && $filters['log_type'] !== 'all') {
            $query->where('log_name', $filters['log_type']);
        }

        return $query->with('causer')
                    ->orderBy('created_at', 'desc')
                    ->limit(10000)
                    ->get();
    }
}
