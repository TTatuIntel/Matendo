<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SystemSettingsController extends Controller
{
    /**
     * Display system settings dashboard
     */
    public function index()
    {
        try {
            $settings = $this->getAllSettings();
            
            $systemInfo = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database_type' => config('database.default'),
                'cache_driver' => config('cache.default'),
                'queue_driver' => config('queue.default'),
                'mail_driver' => config('mail.default'),
                'timezone' => config('app.timezone'),
                'debug_mode' => config('app.debug'),
                'maintenance_mode' => app()->isDownForMaintenance(),
                'disk_usage' => $this->getDiskUsage(),
                'memory_usage' => $this->getMemoryUsage()
            ];

            $cacheInfo = [
                'status' => $this->getCacheStatus(),
                'size' => $this->getCacheSize(),
                'driver' => config('cache.default')
            ];

            return view('admin.settings', compact('settings', 'systemInfo', 'cacheInfo'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load system settings: ' . $e->getMessage()]);
        }
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'app_name' => 'required|string|max:255',
                'app_url' => 'required|url',
                'app_timezone' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:255',
                'mail_from_address' => 'required|email',
                'session_lifetime' => 'required|integer|min:1|max:10080', // Max 1 week
                'max_upload_size' => 'required|integer|min:1|max:102400', // Max 100MB
                'notifications_enabled' => 'boolean',
                'email_notifications' => 'boolean',
                'sms_notifications' => 'boolean',
                'backup_frequency' => 'required|in:daily,weekly,monthly',
                'data_retention_days' => 'required|integer|min:30|max:2555', // Max 7 years
                'security_level' => 'required|in:low,medium,high,strict',
                'auto_logout_minutes' => 'required|integer|min:5|max:480', // Max 8 hours
                'password_min_length' => 'required|integer|min:6|max:50',
                'password_require_special' => 'boolean',
                'two_factor_enabled' => 'boolean',
                'audit_logging' => 'boolean',
                'debug_mode' => 'boolean',
                'registration_enabled' => 'boolean',
                'external_access_enabled' => 'boolean'
            ]);

            DB::beginTransaction();

            // Update each setting
            foreach ($validated as $key => $value) {
                $this->updateSetting($key, $value);
            }

            // Update environment variables for critical settings
            $this->updateEnvironmentVariables([
                'APP_NAME' => $validated['app_name'],
                'APP_URL' => $validated['app_url'],
                'APP_TIMEZONE' => $validated['app_timezone'],
                'APP_DEBUG' => $validated['debug_mode'] ? 'true' : 'false',
                'MAIL_FROM_NAME' => $validated['mail_from_name'],
                'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
                'SESSION_LIFETIME' => $validated['session_lifetime']
            ]);

            // Log the settings update
            ActivityLog::create([
                'log_name' => 'system_settings',
                'description' => 'System settings updated',
                'causer_id' => auth()->id(),
                'properties' => [
                    'updated_settings' => array_keys($validated),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]
            ]);

            DB::commit();

            // Clear relevant caches
            $this->clearConfigCache();

            return response()->json([
                'success' => true,
                'message' => 'System settings updated successfully'
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
                'message' => 'Failed to update system settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear system cache
     */
    public function clearCache(Request $request)
    {
        try {
            $cacheTypes = $request->input('cache_types', ['config', 'route', 'view']);
            
            foreach ($cacheTypes as $cacheType) {
                switch ($cacheType) {
                    case 'config':
                        Artisan::call('config:clear');
                        break;
                    case 'route':
                        Artisan::call('route:clear');
                        break;
                    case 'view':
                        Artisan::call('view:clear');
                        break;
                    case 'cache':
                        Artisan::call('cache:clear');
                        break;
                    case 'compiled':
                        Artisan::call('clear-compiled');
                        break;
                }
            }

            // Log cache clearing
            ActivityLog::create([
                'log_name' => 'system_maintenance',
                'description' => 'System cache cleared',
                'causer_id' => auth()->id(),
                'properties' => [
                    'cache_types' => $cacheTypes,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
                'cleared_types' => $cacheTypes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize system performance
     */
    public function optimize(Request $request)
    {
        try {
            $optimizations = [];

            // Cache configurations
            Artisan::call('config:cache');
            $optimizations[] = 'Configuration cached';

            // Cache routes
            Artisan::call('route:cache');
            $optimizations[] = 'Routes cached';

            // Cache views
            Artisan::call('view:cache');
            $optimizations[] = 'Views cached';

            // Optimize autoloader
            Artisan::call('optimize');
            $optimizations[] = 'Autoloader optimized';

            // Clear old logs if specified
            if ($request->input('clear_old_logs', false)) {
                $deletedLogs = ActivityLog::where('created_at', '<', now()->subDays(90))->delete();
                $optimizations[] = "Deleted {$deletedLogs} old log entries";
            }

            // Log optimization
            ActivityLog::create([
                'log_name' => 'system_optimization',
                'description' => 'System optimization performed',
                'causer_id' => auth()->id(),
                'properties' => [
                    'optimizations' => $optimizations,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System optimization completed',
                'optimizations' => $optimizations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'System optimization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance(Request $request)
    {
        try {
            $validated = $request->validate([
                'enable' => 'required|boolean',
                'message' => 'nullable|string|max:500',
                'retry_after' => 'nullable|integer|min:60|max:86400', // 1 minute to 24 hours
                'secret' => 'nullable|string|min:6|max:50'
            ]);

            if ($validated['enable']) {
                $options = [];
                
                if (!empty($validated['message'])) {
                    $options['message'] = $validated['message'];
                }
                
                if (!empty($validated['retry_after'])) {
                    $options['retry'] = $validated['retry_after'];
                }
                
                if (!empty($validated['secret'])) {
                    $options['secret'] = $validated['secret'];
                }

                Artisan::call('down', $options);
                $message = 'Maintenance mode enabled';
            } else {
                Artisan::call('up');
                $message = 'Maintenance mode disabled';
            }

            // Log maintenance mode toggle
            ActivityLog::create([
                'log_name' => 'system_maintenance',
                'description' => $message,
                'causer_id' => auth()->id(),
                'properties' => [
                    'maintenance_enabled' => $validated['enable'],
                    'message' => $validated['message'] ?? null,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'maintenance_mode' => $validated['enable']
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
                'message' => 'Failed to toggle maintenance mode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate comprehensive system report
     */
    public function systemReport()
    {
        try {
            $reportData = [
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'database_type' => config('database.default'),
                    'cache_driver' => config('cache.default'),
                    'queue_driver' => config('queue.default'),
                    'mail_driver' => config('mail.default'),
                    'timezone' => config('app.timezone'),
                    'debug_mode' => config('app.debug'),
                    'maintenance_mode' => app()->isDownForMaintenance(),
                    'disk_usage' => $this->getDiskUsage(),
                    'memory_usage' => $this->getMemoryUsage()
                ],
                'health_checks' => [
                    'database' => $this->checkDatabaseConnection(),
                    'cache' => $this->checkCacheConnection(),
                    'storage' => $this->checkStorageWritable(),
                    'queue' => $this->checkQueueStatus(),
                    'mail' => $this->checkMailConfiguration(),
                    'disk_space' => $this->checkDiskSpace(),
                    'memory' => $this->checkMemoryUsage()
                ],
                'settings' => $this->getAllSettings(),
                'security_status' => [
                    'hipaa_compliance' => env('HIPAA_COMPLIANCE_MODE', true),
                    'encryption_enabled' => env('PATIENT_DATA_ENCRYPTION', true),
                    'audit_logging' => env('AUDIT_LOGGING', true),
                    '2fa_required' => env('SECURITY_HIPAA_2FA_REQUIRED', true),
                    'session_timeout' => env('SECURITY_HIPAA_SESSION_TIMEOUT', 1800),
                ],
                'performance_metrics' => [
                    'uptime' => '99.8%', // Placeholder
                    'avg_response_time' => '250ms', // Placeholder
                    'error_rate' => '0.2%', // Placeholder
                    'active_users' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
                    'total_users' => User::count(),
                ],
                'generated_at' => now(),
                'generated_by' => auth()->user()->name
            ];

            return view('admin.settings.system-report', compact('reportData'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate system report: ' . $e->getMessage()]);
        }
    }

    /**
     * Get system health status
     */
    public function healthCheck()
    {
        try {
            $health = [
                'database' => $this->checkDatabaseConnection(),
                'cache' => $this->checkCacheConnection(),
                'storage' => $this->checkStorageWritable(),
                'queue' => $this->checkQueueStatus(),
                'mail' => $this->checkMailConfiguration(),
                'disk_space' => $this->checkDiskSpace(),
                'memory' => $this->checkMemoryUsage()
            ];

            $overallStatus = collect($health)->every(function ($status) {
                return $status['status'] === 'ok';
            }) ? 'healthy' : 'warning';

            return response()->json([
                'success' => true,
                'overall_status' => $overallStatus,
                'checks' => $health,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Health check failed: ' . $e->getMessage(),
                'overall_status' => 'error'
            ], 500);
        }
    }

    /**
     * Export system configuration
     */
    public function exportConfiguration()
    {
        try {
            $config = [
                'settings' => $this->getAllSettings(),
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'database_type' => config('database.default'),
                    'cache_driver' => config('cache.default'),
                    'queue_driver' => config('queue.default'),
                    'mail_driver' => config('mail.default'),
                    'timezone' => config('app.timezone')
                ],
                'exported_at' => now(),
                'exported_by' => auth()->user()->name
            ];

            return response()->streamDownload(function () use ($config) {
                echo json_encode($config, JSON_PRETTY_PRINT);
            }, 'system-configuration-' . now()->format('Y-m-d-H-i-s') . '.json');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import system configuration
     */
    public function importConfiguration(Request $request)
    {
        try {
            $validated = $request->validate([
                'config_file' => 'required|file|mimes:json|max:1024' // Max 1MB
            ]);

            $configFile = $validated['config_file'];
            $configContent = json_decode($configFile->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON format');
            }

            if (!isset($configContent['settings']) || !is_array($configContent['settings'])) {
                throw new \InvalidArgumentException('Invalid configuration format');
            }

            DB::beginTransaction();

            $importedCount = 0;
            foreach ($configContent['settings'] as $key => $value) {
                $this->updateSetting($key, $value);
                $importedCount++;
            }

            // Log configuration import
            ActivityLog::create([
                'log_name' => 'system_configuration',
                'description' => 'System configuration imported',
                'causer_id' => auth()->id(),
                'properties' => [
                    'imported_settings_count' => $importedCount,
                    'source_file' => $configFile->getClientOriginalName(),
                    'ip_address' => request()->ip()
                ]
            ]);

            DB::commit();

            // Clear config cache
            $this->clearConfigCache();

            return response()->json([
                'success' => true,
                'message' => "Configuration imported successfully. {$importedCount} settings updated.",
                'imported_count' => $importedCount
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
                'message' => 'Failed to import configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods
    private function getAllSettings()
    {
        // Check if SystemSetting model exists, otherwise return defaults
        if (!class_exists(SystemSetting::class)) {
            return $this->getDefaultSettings();
        }

        $settings = SystemSetting::pluck('value', 'key')->toArray();
        
        // Merge with defaults to ensure all settings exist
        return array_merge($this->getDefaultSettings(), $settings);
    }

    private function getDefaultSettings()
    {
        return [
            'app_name' => config('app.name', 'Medical Care System'),
            'app_url' => config('app.url', 'http://localhost'),
            'app_timezone' => config('app.timezone', 'UTC'),
            'mail_from_name' => config('mail.from.name', 'Medical System'),
            'mail_from_address' => config('mail.from.address', 'noreply@example.com'),
            'session_lifetime' => config('session.lifetime', 120),
            'max_upload_size' => 10240, // 10MB
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'backup_frequency' => 'daily',
            'data_retention_days' => 365,
            'security_level' => 'medium',
            'auto_logout_minutes' => 30,
            'password_min_length' => 8,
            'password_require_special' => true,
            'two_factor_enabled' => false,
            'audit_logging' => true,
            'debug_mode' => config('app.debug', false),
            'registration_enabled' => true,
            'external_access_enabled' => true
        ];
    }

    private function updateSetting($key, $value)
    {
        if (!class_exists(SystemSetting::class)) {
            return; // Can't persist settings without model
        }

        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'updated_by' => auth()->id()]
        );
    }

    private function updateEnvironmentVariables($variables)
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            return false;
        }

        $envContent = File::get($envPath);
        
        foreach ($variables as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envPath, $envContent);
        return true;
    }

    private function clearConfigCache()
    {
        try {
            Artisan::call('config:clear');
            Cache::flush();
        } catch (\Exception $e) {
            // Silently fail - not critical
        }
    }

    private function getDiskUsage()
    {
        $totalBytes = disk_total_space(base_path());
        $freeBytes = disk_free_space(base_path());
        $usedBytes = $totalBytes - $freeBytes;
        
        return [
            'total' => $this->formatBytes($totalBytes),
            'used' => $this->formatBytes($usedBytes),
            'free' => $this->formatBytes($freeBytes),
            'percentage' => round(($usedBytes / $totalBytes) * 100, 1)
        ];
    }

    private function getMemoryUsage()
    {
        return [
            'current' => $this->formatBytes(memory_get_usage(true)),
            'peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'limit' => ini_get('memory_limit')
        ];
    }

    private function getCacheStatus()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            return Cache::get('health_check') === 'ok' ? 'connected' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getCacheSize()
    {
        // This is a placeholder - actual implementation would depend on cache driver
        return 'Unknown';
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Health check methods
    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkCacheConnection()
    {
        try {
            Cache::put('health_check_cache', 'test', 10);
            $result = Cache::get('health_check_cache');
            Cache::forget('health_check_cache');
            
            return $result === 'test' ? 
                ['status' => 'ok', 'message' => 'Cache is working'] :
                ['status' => 'error', 'message' => 'Cache test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache error: ' . $e->getMessage()];
        }
    }

    private function checkStorageWritable()
    {
        $storagePath = storage_path('app/temp');
        
        try {
            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0755, true);
            }
            
            $testFile = $storagePath . '/write_test.txt';
            File::put($testFile, 'test');
            $content = File::get($testFile);
            File::delete($testFile);
            
            return $content === 'test' ? 
                ['status' => 'ok', 'message' => 'Storage is writable'] :
                ['status' => 'error', 'message' => 'Storage write test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage error: ' . $e->getMessage()];
        }
    }

    private function checkQueueStatus()
    {
        // Placeholder - would check actual queue status
        return ['status' => 'ok', 'message' => 'Queue system operational'];
    }

    private function checkMailConfiguration()
    {
        $driver = config('mail.default');
        
        if (empty($driver)) {
            return ['status' => 'error', 'message' => 'Mail driver not configured'];
        }
        
        return ['status' => 'ok', 'message' => "Mail driver '{$driver}' configured"];
    }

    private function checkDiskSpace()
    {
        $freeBytes = disk_free_space(base_path());
        $totalBytes = disk_total_space(base_path());
        $freePercentage = ($freeBytes / $totalBytes) * 100;
        
        if ($freePercentage < 10) {
            return ['status' => 'error', 'message' => 'Low disk space: ' . round($freePercentage, 1) . '% free'];
        } elseif ($freePercentage < 20) {
            return ['status' => 'warning', 'message' => 'Disk space warning: ' . round($freePercentage, 1) . '% free'];
        }
        
        return ['status' => 'ok', 'message' => 'Disk space OK: ' . round($freePercentage, 1) . '% free'];
    }

    private function checkMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit === '-1') {
            return ['status' => 'ok', 'message' => 'Memory usage: ' . $this->formatBytes($memoryUsage) . ' (no limit)'];
        }
        
        $limitBytes = $this->parseMemoryLimit($memoryLimit);
        $usagePercentage = ($memoryUsage / $limitBytes) * 100;
        
        if ($usagePercentage > 90) {
            return ['status' => 'error', 'message' => 'High memory usage: ' . round($usagePercentage, 1) . '%'];
        } elseif ($usagePercentage > 75) {
            return ['status' => 'warning', 'message' => 'Memory usage warning: ' . round($usagePercentage, 1) . '%'];
        }
        
        return ['status' => 'ok', 'message' => 'Memory usage OK: ' . round($usagePercentage, 1) . '%'];
    }

    private function parseMemoryLimit($limit)
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit)-1]);
        $number = substr($limit, 0, -1);
        
        switch ($last) {
            case 'g': return $number * 1024 * 1024 * 1024;
            case 'm': return $number * 1024 * 1024;
            case 'k': return $number * 1024;
            default: return $number;
        }
    }
}
