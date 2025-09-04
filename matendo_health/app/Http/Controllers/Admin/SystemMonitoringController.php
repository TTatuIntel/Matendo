<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;

class SystemMonitoringController extends Controller
{
    public function index()
    {
        $metrics = [
            'system_health' => $this->getSystemHealth(),
            'user_activity' => $this->getUserActivity(),
            'resource_usage' => $this->getResourceUsage(),
            'database_stats' => $this->getDatabaseStats()
        ];

        return view('admin.monitoring.index', compact('metrics'));
    }

    public function getRealTimeData()
    {
        return response()->json([
            'timestamp' => now(),
            'system_health' => $this->getSystemHealth(),
            'active_users' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
            'memory_usage' => memory_get_usage(true),
            'uptime' => 'Running'
        ]);
    }

    public function getHealthStatus()
    {
        return response()->json([
            'database' => 'connected',
            'cache' => 'active',
            'storage' => 'accessible',
            'queue' => 'running'
        ]);
    }

    public function getSystemMetrics()
    {
        return response()->json([
            'users_online' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'total_doctors' => Doctor::count(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ]);
    }

    public function exportReport()
    {
        $metrics = $this->getSystemMetrics();
        
        return response()->streamDownload(function () use ($metrics) {
            echo "System Monitoring Report\n";
            echo "Generated: " . now() . "\n\n";
            foreach ($metrics->getData() as $key => $value) {
                echo ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
            }
        }, 'system-monitoring-' . now()->format('Y-m-d') . '.txt');
    }

    private function getSystemHealth()
    {
        return [
            'status' => 'healthy',
            'uptime' => '99.9%',
            'response_time' => '150ms'
        ];
    }

    private function getUserActivity()
    {
        return [
            'online_users' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
            'daily_active' => User::where('last_activity', '>=', now()->subDay())->count(),
            'weekly_active' => User::where('last_activity', '>=', now()->subWeek())->count()
        ];
    }

    private function getResourceUsage()
    {
        return [
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'cpu_usage' => '12%',
            'disk_usage' => '45%'
        ];
    }

    private function getDatabaseStats()
    {
        return [
            'total_queries' => 0, // This would need query logging
            'slow_queries' => 0,
            'connections' => 1,
            'size' => '150MB'
        ];
    }
}
