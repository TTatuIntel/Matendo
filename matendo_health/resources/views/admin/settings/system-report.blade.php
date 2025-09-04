@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">System Report</h1>
        <p class="text-gray-600 mt-2">Comprehensive system health and configuration report</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System Information -->
        <div class="glass-card rounded-xl p-6 card-hover smooth-transition">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                </svg>
                System Information
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">PHP Version:</span>
                    <span class="font-semibold text-gray-900">{{ $reportData['system_info']['php_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Laravel Version:</span>
                    <span class="font-semibold text-gray-900">{{ $reportData['system_info']['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Database:</span>
                    <span class="font-semibold text-gray-900">{{ ucfirst($reportData['system_info']['database_type']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cache Driver:</span>
                    <span class="font-semibold text-gray-900">{{ ucfirst($reportData['system_info']['cache_driver']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Queue Driver:</span>
                    <span class="font-semibold text-gray-900">{{ ucfirst($reportData['system_info']['queue_driver']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mail Driver:</span>
                    <span class="font-semibold text-gray-900">{{ ucfirst($reportData['system_info']['mail_driver']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Timezone:</span>
                    <span class="font-semibold text-gray-900">{{ $reportData['system_info']['timezone'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Debug Mode:</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $reportData['system_info']['debug_mode'] ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                        {{ $reportData['system_info']['debug_mode'] ? 'ON' : 'OFF' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Maintenance:</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $reportData['system_info']['maintenance_mode'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ $reportData['system_info']['maintenance_mode'] ? 'ON' : 'OFF' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Health Checks -->
        <div class="glass-card rounded-xl p-6 card-hover smooth-transition">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Health Checks
            </h2>
            <div class="space-y-3">
                @foreach($reportData['health_checks'] as $check => $result)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $check)) }}:</span>
                    <div class="flex items-center">
                        <span class="px-2 py-1 rounded text-xs font-semibold mr-2 {{ 
                            $result['status'] === 'ok' ? 'bg-green-100 text-green-800' : 
                            ($result['status'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                        }}">
                            {{ ucfirst($result['status']) }}
                        </span>
                        @if($result['status'] === 'ok')
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @elseif($result['status'] === 'warning')
                            <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="text-sm text-gray-500 ml-4">{{ $result['message'] }}</div>
                @endforeach
            </div>
        </div>

        <!-- Security Status -->
        <div class="glass-card rounded-xl p-6 card-hover smooth-transition">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Security Status
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">HIPAA Compliance:</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $reportData['security_status']['hipaa_compliance'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $reportData['security_status']['hipaa_compliance'] ? 'ENABLED' : 'DISABLED' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Data Encryption:</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $reportData['security_status']['encryption_enabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $reportData['security_status']['encryption_enabled'] ? 'ENABLED' : 'DISABLED' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Audit Logging:</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $reportData['security_status']['audit_logging'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $reportData['security_status']['audit_logging'] ? 'ENABLED' : 'DISABLED' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">2FA Required:</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $reportData['security_status']['2fa_required'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $reportData['security_status']['2fa_required'] ? 'ENABLED' : 'DISABLED' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Session Timeout:</span>
                    <span class="font-semibold text-gray-900">{{ $reportData['security_status']['session_timeout'] }} seconds</span>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="glass-card rounded-xl p-6 card-hover smooth-transition">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Performance Metrics
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">System Uptime:</span>
                    <span class="font-semibold text-green-600">{{ $reportData['performance_metrics']['uptime'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Avg Response Time:</span>
                    <span class="font-semibold text-gray-900">{{ $reportData['performance_metrics']['avg_response_time'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Error Rate:</span>
                    <span class="font-semibold text-gray-900">{{ $reportData['performance_metrics']['error_rate'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Users:</span>
                    <span class="font-semibold text-blue-600">{{ $reportData['performance_metrics']['active_users'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Users:</span>
                    <span class="font-semibold text-gray-900">{{ $reportData['performance_metrics']['total_users'] }}</span>
                </div>
            </div>
        </div>

        <!-- Resource Usage -->
        <div class="glass-card rounded-xl p-6 card-hover smooth-transition lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                </svg>
                Resource Usage
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Disk Usage -->
                <div>
                    <h3 class="font-medium text-gray-700 mb-3">Disk Usage</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Used:</span>
                            <span>{{ $reportData['system_info']['disk_usage']['used'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Free:</span>
                            <span>{{ $reportData['system_info']['disk_usage']['free'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Total:</span>
                            <span>{{ $reportData['system_info']['disk_usage']['total'] }}</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Usage</span>
                                <span>{{ $reportData['system_info']['disk_usage']['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" style="width: {{ $reportData['system_info']['disk_usage']['percentage'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Memory Usage -->
                <div>
                    <h3 class="font-medium text-gray-700 mb-3">Memory Usage</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Current:</span>
                            <span>{{ $reportData['system_info']['memory_usage']['current'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Peak:</span>
                            <span>{{ $reportData['system_info']['memory_usage']['peak'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Limit:</span>
                            <span>{{ $reportData['system_info']['memory_usage']['limit'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Footer -->
    <div class="mt-8 glass-card rounded-xl p-6">
        <div class="flex justify-between items-center text-sm text-gray-600">
            <div>
                <span>Generated at: {{ $reportData['generated_at']->format('Y-m-d H:i:s') }}</span>
            </div>
            <div>
                <span>Generated by: {{ $reportData['generated_by'] }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
