<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class AdminProfileController extends Controller
{
    /**
     * Display admin profile
     */
    public function edit()
    {
        try {
            $admin = auth()->user();
            
            // Get admin activity statistics
            $activityStats = [
                'login_sessions_this_month' => ActivityLog::where('causer_id', $admin->id)
                    ->where('log_name', 'auth')
                    ->where('description', 'like', '%login%')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                
                'actions_performed_today' => ActivityLog::where('causer_id', $admin->id)
                    ->whereDate('created_at', today())
                    ->count(),
                    
                'users_managed_this_month' => ActivityLog::where('causer_id', $admin->id)
                    ->where('log_name', 'user_management')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                    
                'security_actions_this_week' => ActivityLog::where('causer_id', $admin->id)
                    ->where('log_name', 'security')
                    ->where('created_at', '>=', now()->subWeek())
                    ->count()
            ];

            // Get recent activities
            $recentActivities = ActivityLog::where('causer_id', $admin->id)
                                         ->orderBy('created_at', 'desc')
                                         ->limit(10)
                                         ->get()
                                         ->map(function($log) {
                                             return [
                                                 'description' => $log->description,
                                                 'log_name' => $log->log_name,
                                                 'created_at' => $log->created_at,
                                                 'ip_address' => $log->properties['ip_address'] ?? 'Unknown'
                                             ];
                                         });

            // Get security summary
            $securitySummary = [
                'last_login' => $admin->last_activity,
                'account_created' => $admin->created_at,
                'password_last_changed' => $admin->password_changed_at ?? $admin->created_at,
                'failed_login_attempts' => ActivityLog::where('log_name', 'auth')
                    ->where('description', 'like', '%failed%')
                    ->whereJsonContains('properties->email', $admin->email)
                    ->where('created_at', '>=', now()->subMonth())
                    ->count(),
                'two_factor_enabled' => $admin->two_factor_enabled ?? false,
                'active_sessions' => 1 // Current session
            ];

            return view('admin.profile.edit', compact(
                'admin', 
                'activityStats', 
                'recentActivities', 
                'securitySummary'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Update admin profile information
     */
    public function update(Request $request)
    {
        try {
            $admin = auth()->user();

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
                'phone' => ['nullable', 'string', 'max:20'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'timezone' => ['nullable', 'string', 'max:50'],
                'language' => ['nullable', 'string', 'max:10'],
                'date_format' => ['nullable', 'string', 'max:20'],
                'notifications_email' => ['boolean'],
                'notifications_push' => ['boolean'],
                'notifications_sms' => ['boolean'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
            ]);

            DB::beginTransaction();

            // Handle avatar upload
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($admin->avatar && Storage::exists($admin->avatar)) {
                    Storage::delete($admin->avatar);
                }

                $avatarPath = $request->file('avatar')->store('avatars/admin', 'public');
                $validated['avatar'] = $avatarPath;
            }

            // Update admin profile
            $admin->update(array_filter($validated, function($value) {
                return $value !== null;
            }));

            // Log profile update
            ActivityLog::create([
                'log_name' => 'admin_profile',
                'description' => "Admin profile updated: {$admin->name}",
                'causer_id' => $admin->id,
                'subject_id' => $admin->id,
                'properties' => [
                    'updated_fields' => array_keys(array_filter($validated, function($value) {
                        return $value !== null;
                    })),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'avatar_url' => $avatarPath ? asset('storage/' . $avatarPath) : null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $admin = auth()->user();

            $admin->update([
                'password' => Hash::make($validated['password']),
                'password_changed_at' => now()
            ]);

            // Log password change
            ActivityLog::create([
                'log_name' => 'admin_security',
                'description' => "Admin password changed: {$admin->name}",
                'causer_id' => $admin->id,
                'subject_id' => $admin->id,
                'properties' => [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
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
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enable/disable two-factor authentication
     */
    public function toggleTwoFactor(Request $request)
    {
        try {
            $admin = auth()->user();
            $newStatus = !($admin->two_factor_enabled ?? false);

            $admin->update([
                'two_factor_enabled' => $newStatus,
                'two_factor_enabled_at' => $newStatus ? now() : null
            ]);

            // Log 2FA change
            ActivityLog::create([
                'log_name' => 'admin_security',
                'description' => "Two-factor authentication " . ($newStatus ? 'enabled' : 'disabled') . ": {$admin->name}",
                'causer_id' => $admin->id,
                'subject_id' => $admin->id,
                'properties' => [
                    'two_factor_status' => $newStatus,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication ' . ($newStatus ? 'enabled' : 'disabled'),
                'two_factor_enabled' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle two-factor authentication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin activity logs
     */
    public function getActivityLogs(Request $request)
    {
        try {
            $admin = auth()->user();
            
            $logs = ActivityLog::where('causer_id', $admin->id)
                             ->when($request->log_name, function($q, $logName) {
                                 $q->where('log_name', $logName);
                             })
                             ->when($request->date_from, function($q, $date) {
                                 $q->whereDate('created_at', '>=', $date);
                             })
                             ->when($request->date_to, function($q, $date) {
                                 $q->whereDate('created_at', '<=', $date);
                             })
                             ->orderBy('created_at', 'desc')
                             ->paginate(20);

            return response()->json([
                'success' => true,
                'logs' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load activity logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export admin activity data
     */
    public function exportActivity(Request $request)
    {
        try {
            $admin = auth()->user();
            $logs = ActivityLog::where('causer_id', $admin->id)
                             ->orderBy('created_at', 'desc')
                             ->limit(1000)
                             ->get();

            return response()->streamDownload(function () use ($logs, $admin) {
                echo "Admin Activity Export for {$admin->name}\n";
                echo "Generated: " . now() . "\n\n";
                echo "Timestamp,Action,Log Type,Description,IP Address\n";
                
                foreach ($logs as $log) {
                    echo implode(',', [
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->log_name ?? 'N/A',
                        $log->description ?? 'No description',
                        $log->properties['ip_address'] ?? 'N/A'
                    ]) . "\n";
                }
            }, "admin-activity-{$admin->id}-" . now()->format('Y-m-d') . '.csv');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to export activity: ' . $e->getMessage()]);
        }
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request)
    {
        try {
            $validated = $request->validate([
                'email_notifications' => ['boolean'],
                'push_notifications' => ['boolean'],
                'sms_notifications' => ['boolean'],
                'notification_frequency' => ['required', 'in:immediate,daily,weekly'],
                'alert_types' => ['array'],
                'alert_types.*' => ['string', 'in:security,user_activity,system,critical_alerts']
            ]);

            $admin = auth()->user();
            
            $admin->update([
                'notification_preferences' => json_encode($validated)
            ]);

            // Log preference update
            ActivityLog::create([
                'log_name' => 'admin_profile',
                'description' => "Notification preferences updated: {$admin->name}",
                'causer_id' => $admin->id,
                'subject_id' => $admin->id,
                'properties' => [
                    'preferences' => $validated,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully'
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
                'message' => 'Failed to update notification preferences: ' . $e->getMessage()
            ], 500);
        }
    }
}
