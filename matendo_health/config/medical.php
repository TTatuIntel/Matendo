<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Medical Care System Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings specific to the Medical Care System.
    |
    */

    'system' => [
        'name' => env('APP_NAME', 'Medical Care System'),
        'version' => env('SYSTEM_VERSION', '1.0.0'),
        'timezone' => env('APP_TIMEZONE', 'UTC'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HIPAA Compliance Settings
    |--------------------------------------------------------------------------
    |
    | Settings to ensure HIPAA compliance for handling protected health
    | information (PHI).
    |
    */
    'hipaa' => [
        'compliance_mode' => env('HIPAA_COMPLIANCE_MODE', true),
        'strict_mode' => env('SECURITY_HIPAA_STRICT_MODE', true),
        'encrypt_phi' => env('PATIENT_DATA_ENCRYPTION', true),
        'audit_all_access' => env('AUDIT_LOGGING', true),
        'minimum_password_length' => 12,
        'password_complexity_required' => true,
        'session_timeout_minutes' => env('SECURITY_HIPAA_SESSION_TIMEOUT', 1800) / 60, // Convert seconds to minutes
        'require_2fa_medical_access' => env('SECURITY_HIPAA_2FA_REQUIRED', true),
        'anonymize_logs' => env('SECURITY_HIPAA_ANONYMIZE_LOGS', true),
        'data_retention_years' => env('SECURITY_AUDIT_RETENTION_DAYS', 2555) / 365, // Convert days to years
    ],

    /*
    |--------------------------------------------------------------------------
    | Patient Data Management
    |--------------------------------------------------------------------------
    |
    | Configuration for patient data handling and security.
    |
    */
    'patient' => [
        'auto_lock_records' => true,
        'lock_timeout_minutes' => 30,
        'require_access_reason' => true,
        'enable_patient_portal' => true,
        'allow_patient_data_download' => false,
        'encrypt_at_rest' => env('SECURITY_HIPAA_ENCRYPT_AT_REST', true),
        'backup_encryption' => true,
        'anonymize_for_research' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Medical Records Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for medical records management and access control.
    |
    */
    'records' => [
        'default_access_level' => 'restricted',
        'enable_versioning' => true,
        'max_versions_to_keep' => 50,
        'auto_backup_interval_hours' => 6,
        'require_digital_signature' => true,
        'enable_record_locking' => true,
        'concurrent_access_limit' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Emergency Access Configuration
    |--------------------------------------------------------------------------
    |
    | Break-glass access settings for emergency situations.
    |
    */
    'emergency' => [
        'enable_break_glass' => env('SECURITY_BREAK_GLASS_ENABLED', true),
        'require_justification' => env('SECURITY_EMERGENCY_REQUIRE_JUSTIFICATION', true),
        'access_duration_minutes' => env('SECURITY_EMERGENCY_ACCESS_DURATION', 3600) / 60,
        'audit_level' => 'critical',
        'notify_administrators' => true,
        'auto_revoke_after_use' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit and Compliance Logging
    |--------------------------------------------------------------------------
    |
    | Comprehensive logging configuration for audit trails and compliance.
    |
    */
    'audit' => [
        'log_medical_access' => env('SECURITY_LOG_MEDICAL_ACCESS', true),
        'log_admin_actions' => env('SECURITY_LOG_ADMIN_ACTIONS', true),
        'log_data_exports' => true,
        'log_record_modifications' => true,
        'log_user_sessions' => true,
        'log_failed_access_attempts' => true,
        'real_time_monitoring' => env('SECURITY_REAL_TIME_MONITORING', true),
        'alert_on_suspicious_activity' => env('SECURITY_ALERT_SUSPICIOUS', true),
        'retention_years' => env('SECURITY_AUDIT_RETENTION_DAYS', 2555) / 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for integration with external medical systems.
    |
    */
    'integrations' => [
        'hl7_enabled' => false,
        'fhir_enabled' => false,
        'dicom_enabled' => false,
        'lab_systems' => [],
        'pharmacy_systems' => [],
        'insurance_systems' => [],
        'enable_api_access' => true,
        'api_rate_limit' => env('SECURITY_API_RATE_LIMIT', 60),
        'require_api_keys' => env('SECURITY_API_REQUIRE_KEY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    |
    | Settings for healthcare user management and roles.
    |
    */
    'users' => [
        'require_license_verification' => true,
        'license_expiry_warning_days' => 30,
        'enable_role_based_access' => true,
        'default_new_user_role' => 'staff',
        'require_supervisor_approval' => true,
        'session_concurrent_limit' => 1,
        'password_expiry_days' => env('SECURITY_PASSWORD_EXPIRY_DAYS', 90),
        'failed_login_lockout' => env('SECURITY_MAX_FAILED_ATTEMPTS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Backup and Recovery
    |--------------------------------------------------------------------------
    |
    | Configuration for data backup and disaster recovery procedures.
    |
    */
    'backup' => [
        'enable_automated_backups' => true,
        'backup_frequency_hours' => 6,
        'retain_backups_days' => 90,
        'encrypt_backups' => true,
        'verify_backup_integrity' => true,
        'offsite_backup_enabled' => false,
        'backup_notification_email' => env('SECURITY_ALERT_EMAIL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance and Optimization
    |--------------------------------------------------------------------------
    |
    | Settings to optimize system performance while maintaining security.
    |
    */
    'performance' => [
        'enable_caching' => true,
        'cache_patient_data' => false, // Never cache sensitive patient data
        'cache_lookup_data' => true,
        'enable_compression' => true,
        'optimize_database_queries' => true,
        'connection_pool_size' => 10,
        'query_timeout_seconds' => 30,
    ],
];
