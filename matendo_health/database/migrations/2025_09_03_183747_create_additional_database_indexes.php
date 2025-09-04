<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for Users table
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'idx_users_role');
                $table->index('status', 'idx_users_status');
                $table->index('created_at', 'idx_users_created_at');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }

        // Add indexes for Patients table
        try {
            Schema::table('patients', function (Blueprint $table) {
                $table->index('user_id', 'idx_patients_user_id');
                $table->index('medical_record_number', 'idx_patients_mrn');
                $table->index('date_of_birth', 'idx_patients_dob');
                $table->index('blood_type', 'idx_patients_blood_type');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }

        // Add indexes for Doctors table
        try {
            Schema::table('doctors', function (Blueprint $table) {
                $table->index('user_id', 'idx_doctors_user_id');
                $table->index('license_number', 'idx_doctors_license');
                $table->index('specialization', 'idx_doctors_specialization');
                $table->index('verification_status', 'idx_doctors_verification');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }

        // Add indexes for VitalSigns table
        try {
            Schema::table('vital_signs', function (Blueprint $table) {
                $table->index('patient_id', 'idx_vitals_patient_id');
                $table->index('measured_at', 'idx_vitals_measured_at');
                $table->index(['patient_id', 'measured_at'], 'idx_vitals_patient_measured');
                $table->index('heart_rate', 'idx_vitals_heart_rate');
                $table->index('systolic_bp', 'idx_vitals_systolic');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }

        // Add indexes for Alerts table
        try {
            Schema::table('alerts', function (Blueprint $table) {
                $table->index('patient_id', 'idx_alerts_patient_id');
                $table->index('severity', 'idx_alerts_severity');
                $table->index('status', 'idx_alerts_status');
                $table->index('alert_type', 'idx_alerts_type');
                $table->index(['severity', 'status'], 'idx_alerts_severity_status');
                $table->index('created_at', 'idx_alerts_created_at');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }

        // Add indexes for Documents table
        try {
            Schema::table('documents', function (Blueprint $table) {
                $table->index('patient_id', 'idx_documents_patient_id');
                $table->index('doctor_id', 'idx_documents_doctor_id');
                $table->index('document_type', 'idx_documents_type');
                $table->index('status', 'idx_documents_status');
                $table->index('created_at', 'idx_documents_created_at');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }

        // Add indexes for TempAccess table
        try {
            Schema::table('temp_accesses', function (Blueprint $table) {
                $table->index('patient_id', 'idx_temp_access_patient_id');
                $table->index('generated_by', 'idx_temp_access_generated_by');
                $table->index('is_active', 'idx_temp_access_is_active');
                $table->index('expires_at', 'idx_temp_access_expires_at');
                $table->index('token', 'idx_temp_access_token');
                $table->index(['is_active', 'expires_at'], 'idx_temp_access_active_expires');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }

        // Add indexes for Blocked IPs table
        try {
            Schema::table('blocked_ips', function (Blueprint $table) {
                $table->index('expires_at', 'idx_blocked_ips_expires_at');
                $table->index('blocked_at', 'idx_blocked_ips_blocked_at');
            });
        } catch (Exception $e) {
            // Skip if already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes - we use try/catch to avoid errors if index doesn't exist
        $indexes = [
            'users' => ['idx_users_role', 'idx_users_status', 'idx_users_created_at'],
            'patients' => ['idx_patients_user_id', 'idx_patients_mrn', 'idx_patients_dob', 'idx_patients_blood_type'],
            'doctors' => ['idx_doctors_user_id', 'idx_doctors_license', 'idx_doctors_specialization', 'idx_doctors_verification'],
            'vital_signs' => ['idx_vitals_patient_id', 'idx_vitals_measured_at', 'idx_vitals_patient_measured', 'idx_vitals_heart_rate', 'idx_vitals_systolic'],
            'alerts' => ['idx_alerts_patient_id', 'idx_alerts_severity', 'idx_alerts_status', 'idx_alerts_type', 'idx_alerts_severity_status', 'idx_alerts_created_at'],
            'documents' => ['idx_documents_patient_id', 'idx_documents_doctor_id', 'idx_documents_type', 'idx_documents_status', 'idx_documents_created_at'],
            'temp_accesses' => ['idx_temp_access_patient_id', 'idx_temp_access_generated_by', 'idx_temp_access_is_active', 'idx_temp_access_expires_at', 'idx_temp_access_token', 'idx_temp_access_active_expires'],
            'blocked_ips' => ['idx_blocked_ips_expires_at', 'idx_blocked_ips_blocked_at'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) use ($tableIndexes) {
                    foreach ($tableIndexes as $index) {
                        try {
                            $blueprint->dropIndex($index);
                        } catch (Exception $e) {
                            // Index doesn't exist, continue
                        }
                    }
                });
            }
        }
    }
};
