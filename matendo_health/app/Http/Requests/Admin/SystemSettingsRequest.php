<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SystemSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-system-settings') || $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return match ($this->route()->getName()) {
            'admin.settings.update' => $this->settingsUpdateRules(),
            'admin.system.clear-cache' => $this->clearCacheRules(),
            'admin.system.maintenance' => $this->maintenanceRules(),
            'admin.settings.import' => $this->importConfigRules(),
            default => []
        };
    }

    /**
     * Rules for system settings update
     */
    private function settingsUpdateRules(): array
    {
        return [
            'app_name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-_]+$/',
            'app_url' => 'required|url|max:255',
            'app_timezone' => [
                'required',
                'string',
                'max:50',
                Rule::in(timezone_identifiers_list())
            ],
            'mail_from_name' => 'required|string|max:255',
            'mail_from_address' => 'required|email|max:255',
            'session_lifetime' => 'required|integer|min:1|max:10080', // Max 1 week in minutes
            'max_upload_size' => 'required|integer|min:1|max:102400', // Max 100MB in KB
            'notifications_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'data_retention_days' => 'required|integer|min:30|max:2555', // Min 30 days, Max 7 years
            'security_level' => 'required|in:low,medium,high,strict',
            'auto_logout_minutes' => 'required|integer|min:5|max:480', // Min 5 minutes, Max 8 hours
            'password_min_length' => 'required|integer|min:6|max:50',
            'password_require_special' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'audit_logging' => 'boolean',
            'debug_mode' => 'boolean',
            'registration_enabled' => 'boolean',
            'external_access_enabled' => 'boolean'
        ];
    }

    /**
     * Rules for cache clearing
     */
    private function clearCacheRules(): array
    {
        return [
            'cache_types' => 'required|array|min:1',
            'cache_types.*' => 'required|string|in:config,route,view,cache,compiled'
        ];
    }

    /**
     * Rules for maintenance mode
     */
    private function maintenanceRules(): array
    {
        return [
            'enable' => 'required|boolean',
            'message' => 'nullable|string|max:500',
            'retry_after' => 'nullable|integer|min:60|max:86400', // 1 minute to 24 hours
            'secret' => 'nullable|string|min:6|max:50|alpha_num'
        ];
    }

    /**
     * Rules for configuration import
     */
    private function importConfigRules(): array
    {
        return [
            'config_file' => 'required|file|mimes:json|max:1024' // Max 1MB
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'app_name.required' => 'Application name is required.',
            'app_name.regex' => 'Application name can only contain letters, numbers, spaces, hyphens and underscores.',
            'app_url.required' => 'Application URL is required.',
            'app_url.url' => 'Please provide a valid URL.',
            'app_timezone.required' => 'Application timezone is required.',
            'app_timezone.in' => 'Please select a valid timezone.',
            'mail_from_address.email' => 'Please provide a valid email address.',
            'session_lifetime.min' => 'Session lifetime must be at least 1 minute.',
            'session_lifetime.max' => 'Session lifetime cannot exceed 1 week.',
            'max_upload_size.min' => 'Maximum upload size must be at least 1KB.',
            'max_upload_size.max' => 'Maximum upload size cannot exceed 100MB.',
            'data_retention_days.min' => 'Data retention period must be at least 30 days.',
            'data_retention_days.max' => 'Data retention period cannot exceed 7 years.',
            'auto_logout_minutes.min' => 'Auto logout time must be at least 5 minutes.',
            'auto_logout_minutes.max' => 'Auto logout time cannot exceed 8 hours.',
            'password_min_length.min' => 'Minimum password length must be at least 6 characters.',
            'password_min_length.max' => 'Minimum password length cannot exceed 50 characters.',
            'cache_types.required' => 'Please select at least one cache type to clear.',
            'cache_types.min' => 'Please select at least one cache type.',
            'message.max' => 'Maintenance message cannot exceed 500 characters.',
            'retry_after.min' => 'Retry after time must be at least 1 minute.',
            'retry_after.max' => 'Retry after time cannot exceed 24 hours.',
            'secret.min' => 'Maintenance secret must be at least 6 characters.',
            'secret.max' => 'Maintenance secret cannot exceed 50 characters.',
            'secret.alpha_num' => 'Maintenance secret can only contain letters and numbers.',
            'config_file.required' => 'Configuration file is required.',
            'config_file.mimes' => 'Configuration file must be in JSON format.',
            'config_file.max' => 'Configuration file cannot exceed 1MB.'
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'app_name' => 'application name',
            'app_url' => 'application URL',
            'app_timezone' => 'application timezone',
            'mail_from_name' => 'mail from name',
            'mail_from_address' => 'mail from address',
            'session_lifetime' => 'session lifetime',
            'max_upload_size' => 'maximum upload size',
            'notifications_enabled' => 'notifications enabled',
            'email_notifications' => 'email notifications',
            'sms_notifications' => 'SMS notifications',
            'backup_frequency' => 'backup frequency',
            'data_retention_days' => 'data retention days',
            'security_level' => 'security level',
            'auto_logout_minutes' => 'auto logout minutes',
            'password_min_length' => 'minimum password length',
            'password_require_special' => 'require special characters',
            'two_factor_enabled' => 'two-factor authentication enabled',
            'audit_logging' => 'audit logging enabled',
            'debug_mode' => 'debug mode enabled',
            'registration_enabled' => 'registration enabled',
            'external_access_enabled' => 'external access enabled',
            'cache_types' => 'cache types',
            'retry_after' => 'retry after',
            'config_file' => 'configuration file'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation logic
            if ($this->isMethod('POST') && $this->routeIs('admin.system.maintenance')) {
                $this->validateMaintenanceMode($validator);
            }

            if ($this->isMethod('POST') && $this->routeIs('admin.settings.update')) {
                $this->validateSystemSettings($validator);
            }
        });
    }

    /**
     * Custom validation for maintenance mode
     */
    private function validateMaintenanceMode($validator)
    {
        if ($this->input('enable') && empty($this->input('message'))) {
            $validator->errors()->add('message', 'Maintenance message is required when enabling maintenance mode.');
        }

        // Validate secret format if provided
        if ($this->filled('secret')) {
            $secret = $this->input('secret');
            if (!preg_match('/^[a-zA-Z0-9]{6,50}$/', $secret)) {
                $validator->errors()->add('secret', 'Maintenance secret must be 6-50 alphanumeric characters.');
            }
        }
    }

    /**
     * Custom validation for system settings
     */
    private function validateSystemSettings($validator)
    {
        // Validate session lifetime is reasonable
        $sessionLifetime = (int) $this->input('session_lifetime', 0);
        if ($sessionLifetime > 0 && $sessionLifetime < 5) {
            $validator->errors()->add('session_lifetime', 'Session lifetime should be at least 5 minutes for security.');
        }

        // Validate password policies
        $passwordMinLength = (int) $this->input('password_min_length', 8);
        $requireSpecial = $this->boolean('password_require_special');
        
        if ($passwordMinLength < 8 && $requireSpecial) {
            $validator->errors()->add('password_min_length', 'Password minimum length should be at least 8 characters when special characters are required.');
        }

        // Validate security level consistency
        $securityLevel = $this->input('security_level');
        $twoFactorEnabled = $this->boolean('two_factor_enabled');
        
        if ($securityLevel === 'strict' && !$twoFactorEnabled) {
            $validator->errors()->add('two_factor_enabled', 'Two-factor authentication should be enabled for strict security level.');
        }

        // Validate email settings
        if ($this->boolean('email_notifications') && !filter_var($this->input('mail_from_address'), FILTER_VALIDATE_EMAIL)) {
            $validator->errors()->add('mail_from_address', 'Valid mail from address is required when email notifications are enabled.');
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log validation failures for security monitoring
        activity()
            ->causedBy($this->user())
            ->withProperties([
                'errors' => $validator->errors()->toArray(),
                'input' => $this->except(['password', 'secret']),
                'ip_address' => $this->ip(),
                'user_agent' => $this->userAgent()
            ])
            ->log('Validation failed for system settings');

        parent::failedValidation($validator);
    }
}
