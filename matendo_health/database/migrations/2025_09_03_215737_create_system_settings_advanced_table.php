<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if system_settings table already exists
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique()->index();
                $table->longText('value')->nullable();
                $table->enum('type', ['string', 'integer', 'boolean', 'float', 'array', 'json'])->default('string');
                $table->string('group', 50)->default('general')->index();
                $table->string('description')->nullable();
                $table->boolean('is_public')->default(false);
                $table->text('validation_rules')->nullable();
                $table->longText('default_value')->nullable();
                $table->timestamps();
            });
            
            // Insert default system settings
            $this->insertDefaultSettings();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }

    /**
     * Insert default system settings
     */
    private function insertDefaultSettings(): void
    {
        $settings = [
            // Application Settings
            [
                'key' => 'app_name',
                'value' => 'Medical Care System',
                'type' => 'string',
                'group' => 'application',
                'description' => 'Application name displayed in UI',
                'is_public' => true,
                'default_value' => 'Medical Care System'
            ],
            [
                'key' => 'app_version',
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'application',
                'description' => 'Current application version',
                'is_public' => true,
                'default_value' => '1.0.0'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'application',
                'description' => 'Enable maintenance mode',
                'is_public' => false,
                'default_value' => 'false'
            ],

            // Notification Settings
            [
                'key' => 'notifications_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable system notifications',
                'is_public' => false,
                'default_value' => 'true'
            ],
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications',
                'is_public' => false,
                'default_value' => 'true'
            ],
            [
                'key' => 'sms_notifications',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable SMS notifications',
                'is_public' => false,
                'default_value' => 'false'
            ],

            // Security Settings
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Session lifetime in minutes',
                'is_public' => false,
                'default_value' => '120'
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Minimum password length',
                'is_public' => false,
                'default_value' => '8'
            ],
            [
                'key' => 'two_factor_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Enable two-factor authentication',
                'is_public' => false,
                'default_value' => 'false'
            ],
            [
                'key' => 'auto_logout_minutes',
                'value' => '30',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Auto logout after inactivity (minutes)',
                'is_public' => false,
                'default_value' => '30'
            ],

            // System Settings
            [
                'key' => 'max_upload_size',
                'value' => '10240',
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Maximum file upload size in KB',
                'is_public' => false,
                'default_value' => '10240'
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Backup frequency (daily, weekly, monthly)',
                'is_public' => false,
                'default_value' => 'daily'
            ],
            [
                'key' => 'data_retention_days',
                'value' => '365',
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Data retention period in days',
                'is_public' => false,
                'default_value' => '365'
            ],

            // Medical Settings
            [
                'key' => 'external_access_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'medical',
                'description' => 'Allow external doctor access',
                'is_public' => false,
                'default_value' => 'true'
            ],
            [
                'key' => 'patient_registration_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'medical',
                'description' => 'Allow patient self-registration',
                'is_public' => true,
                'default_value' => 'true'
            ],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
        }

        DB::table('system_settings')->insert($settings);
    }
};
