<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting per IP address to prevent abuse.
    |
    */
    'rate_limit' => [
        'max_requests' => env('SECURITY_RATE_LIMIT_REQUESTS', 120), // Per window
        'window' => env('SECURITY_RATE_LIMIT_WINDOW', 3600), // 1 hour in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Security
    |--------------------------------------------------------------------------
    |
    | Configuration for authentication security measures.
    |
    */
    'auth' => [
        'max_failed_attempts' => env('SECURITY_MAX_FAILED_ATTEMPTS', 5),
        'lockout_duration' => env('SECURITY_LOCKOUT_DURATION', 900), // 15 minutes
        'password_history_length' => env('SECURITY_PASSWORD_HISTORY', 5),
        'password_expiry_days' => env('SECURITY_PASSWORD_EXPIRY_DAYS', 90),
        'session_timeout' => env('SECURITY_SESSION_TIMEOUT', 7200), // 2 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    |
    | Security settings for input validation and sanitization.
    |
    */
    'input' => [
        'max_input_length' => env('SECURITY_MAX_INPUT_LENGTH', 10000),
        'max_file_size' => env('SECURITY_MAX_FILE_SIZE', 10 * 1024 * 1024), // 10MB
        'allowed_file_types' => [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'],
            'documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
            'medical' => ['dcm', 'dicom', 'hl7'],
        ],
        'blocked_extensions' => [
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
            'php', 'asp', 'aspx', 'jsp', 'py', 'pl', 'sh', 'cgi'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Address Security
    |--------------------------------------------------------------------------
    |
    | Configuration for IP-based security measures.
    |
    */
    'ip_security' => [
        'enable_geolocation_check' => env('SECURITY_ENABLE_GEOLOCATION', false),
        'allowed_countries' => env('SECURITY_ALLOWED_COUNTRIES', null), // Comma-separated ISO codes
        'blocked_countries' => env('SECURITY_BLOCKED_COUNTRIES', null), // Comma-separated ISO codes
        'whitelist' => [
            // Add whitelisted IP ranges here
            // '192.168.1.0/24',
            // '10.0.0.0/8',
        ],
        'auto_block_duration' => env('SECURITY_AUTO_BLOCK_DURATION', 24 * 60 * 60), // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure security headers to be sent with responses.
    |
    */
    'headers' => [
        'hsts' => [
            'enabled' => env('SECURITY_HSTS_ENABLED', true),
            'max_age' => env('SECURITY_HSTS_MAX_AGE', 31536000), // 1 year
            'include_subdomains' => env('SECURITY_HSTS_SUBDOMAINS', true),
            'preload' => env('SECURITY_HSTS_PRELOAD', false),
        ],
        'csp' => [
            'enabled' => env('SECURITY_CSP_ENABLED', true),
            'policy' => env('SECURITY_CSP_POLICY', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'"),
        ],
        'x_frame_options' => env('SECURITY_X_FRAME_OPTIONS', 'DENY'),
        'x_content_type_options' => env('SECURITY_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'x_xss_protection' => env('SECURITY_X_XSS_PROTECTION', '1; mode=block'),
        'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Encryption
    |--------------------------------------------------------------------------
    |
    | Configuration for additional data encryption beyond Laravel's default.
    |
    */
    'encryption' => [
        'medical_data_encryption' => env('SECURITY_ENCRYPT_MEDICAL_DATA', true),
        'pii_encryption' => env('SECURITY_ENCRYPT_PII', true),
        'encryption_algorithm' => env('SECURITY_ENCRYPTION_ALGORITHM', 'AES-256-GCM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | Configuration for security audit logging.
    |
    */
    'audit' => [
        'log_all_requests' => env('SECURITY_LOG_ALL_REQUESTS', false),
        'log_failed_requests' => env('SECURITY_LOG_FAILED_REQUESTS', true),
        'log_admin_actions' => env('SECURITY_LOG_ADMIN_ACTIONS', true),
        'log_medical_access' => env('SECURITY_LOG_MEDICAL_ACCESS', true),
        'log_sensitive_operations' => env('SECURITY_LOG_SENSITIVE_OPS', true),
        'retention_days' => env('SECURITY_AUDIT_RETENTION_DAYS', 2555), // 7 years for medical compliance
    ],

    /*
    |--------------------------------------------------------------------------
    | Medical Data Security (HIPAA Compliance)
    |--------------------------------------------------------------------------
    |
    | Special security measures for medical data to ensure HIPAA compliance.
    |
    */
    'hipaa' => [
        'enable_strict_mode' => env('SECURITY_HIPAA_STRICT_MODE', true),
        'require_2fa_for_medical_access' => env('SECURITY_HIPAA_2FA_REQUIRED', true),
        'log_all_medical_access' => env('SECURITY_HIPAA_LOG_ACCESS', true),
        'encrypt_medical_data_at_rest' => env('SECURITY_HIPAA_ENCRYPT_AT_REST', true),
        'anonymize_logs' => env('SECURITY_HIPAA_ANONYMIZE_LOGS', true),
        'session_timeout_medical' => env('SECURITY_HIPAA_SESSION_TIMEOUT', 1800), // 30 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Emergency Access
    |--------------------------------------------------------------------------
    |
    | Configuration for emergency access scenarios.
    |
    */
    'emergency' => [
        'enable_break_glass' => env('SECURITY_BREAK_GLASS_ENABLED', true),
        'break_glass_audit_level' => 'critical',
        'emergency_access_duration' => env('SECURITY_EMERGENCY_ACCESS_DURATION', 3600), // 1 hour
        'require_justification' => env('SECURITY_EMERGENCY_REQUIRE_JUSTIFICATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Security
    |--------------------------------------------------------------------------
    |
    | Database-specific security configurations.
    |
    */
    'database' => [
        'enable_query_logging' => env('SECURITY_DB_QUERY_LOGGING', false),
        'log_slow_queries' => env('SECURITY_DB_LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('SECURITY_DB_SLOW_QUERY_THRESHOLD', 2000), // milliseconds
        'enable_read_only_mode' => env('SECURITY_DB_READ_ONLY_MODE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    |
    | Security settings for API endpoints.
    |
    */
    'api' => [
        'rate_limit_per_minute' => env('SECURITY_API_RATE_LIMIT', 60),
        'require_api_key' => env('SECURITY_API_REQUIRE_KEY', true),
        'api_key_rotation_days' => env('SECURITY_API_KEY_ROTATION_DAYS', 30),
        'enable_cors' => env('SECURITY_API_CORS_ENABLED', true),
        'allowed_origins' => env('SECURITY_API_ALLOWED_ORIGINS', '*'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Alerting
    |--------------------------------------------------------------------------
    |
    | Configuration for security monitoring and alerting.
    |
    */
    'monitoring' => [
        'enable_real_time_monitoring' => env('SECURITY_REAL_TIME_MONITORING', true),
        'alert_on_suspicious_activity' => env('SECURITY_ALERT_SUSPICIOUS', true),
        'alert_on_failed_logins' => env('SECURITY_ALERT_FAILED_LOGINS', true),
        'alert_threshold_failed_logins' => env('SECURITY_ALERT_THRESHOLD_FAILED_LOGINS', 10),
        'alert_email' => env('SECURITY_ALERT_EMAIL', null),
        'alert_slack_webhook' => env('SECURITY_ALERT_SLACK_WEBHOOK', null),
    ],
];
