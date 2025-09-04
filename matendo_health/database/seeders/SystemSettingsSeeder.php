<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Application Settings
            [
                'key' => 'app_name',
                'value' => 'Medical Care System',
                'type' => 'string',
                'category' => 'application',
                'description' => 'Application name displayed in UI',
                'is_public' => true
            ],
            [
                'key' => 'app_version',
                'value' => '1.0.0',
                'type' => 'string',
                'category' => 'application',
                'description' => 'Current application version',
                'is_public' => true
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'category' => 'application',
                'description' => 'Enable maintenance mode',
                'is_public' => false
            ],

            // Notification Settings
            [
                'key' => 'notifications_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'notifications',
                'description' => 'Enable system notifications',
                'is_public' => false
            ],
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'notifications',
                'description' => 'Enable email notifications',
                'is_public' => false
            ],
            [
                'key' => 'sms_notifications',
                'value' => 'false',
                'type' => 'boolean',
                'category' => 'notifications',
                'description' => 'Enable SMS notifications',
                'is_public' => false
            ],

            // Security Settings
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Session lifetime in minutes',
                'is_public' => false
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Minimum password length',
                'is_public' => false
            ],
            [
                'key' => 'two_factor_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Enable two-factor authentication',
                'is_public' => false
            ],
            [
                'key' => 'auto_logout_minutes',
                'value' => '30',
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Auto logout after inactivity (minutes)',
                'is_public' => false
            ],

            // System Settings
            [
                'key' => 'max_upload_size',
                'value' => '10240',
                'type' => 'integer',
                'category' => 'system',
                'description' => 'Maximum file upload size in KB',
                'is_public' => false
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Backup frequency (daily, weekly, monthly)',
                'is_public' => false
            ],
            [
                'key' => 'data_retention_days',
                'value' => '365',
                'type' => 'integer',
                'category' => 'system',
                'description' => 'Data retention period in days',
                'is_public' => false
            ],

            // Medical Settings
            [
                'key' => 'external_access_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'medical',
                'description' => 'Allow external doctor access',
                'is_public' => false
            ],
            [
                'key' => 'patient_registration_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'medical',
                'description' => 'Allow patient self-registration',
                'is_public' => true
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('System settings seeded successfully!');
    }
}
