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
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_email_index')) {
                $table->index('email');
            }
            if (!$this->hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
            if (!$this->hasIndex('users', 'users_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('users', 'users_last_activity_index')) {
                $table->index('last_activity');
            }
            if (!$this->hasIndex('users', 'users_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->hasIndex('users', 'users_name_index')) {
                $table->index('name');
            }
        });

        // Add indexes for Patients table
        Schema::table('patients', function (Blueprint $table) {
            if (!$this->hasIndex('patients', 'patients_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->hasIndex('patients', 'patients_medical_record_number_index')) {
                $table->index('medical_record_number');
            }
            if (!$this->hasIndex('patients', 'patients_blood_type_index')) {
                $table->index('blood_type');
            }
            if (!$this->hasIndex('patients', 'patients_created_at_index')) {
                $table->index('created_at');
            }
        });

        // Add indexes for Doctors table
        Schema::table('doctors', function (Blueprint $table) {
            if (!$this->hasIndex('doctors', 'doctors_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->hasIndex('doctors', 'doctors_license_number_index')) {
                $table->index('license_number');
            }
            if (!$this->hasIndex('doctors', 'doctors_specialization_index')) {
                $table->index('specialization');
            }
            if (!$this->hasIndex('doctors', 'doctors_verification_status_index')) {
                $table->index('verification_status');
            }
            if (!$this->hasIndex('doctors', 'doctors_created_at_index')) {
                $table->index('created_at');
            }
        });

        // Add indexes for VitalSigns table
        Schema::table('vital_signs', function (Blueprint $table) {
            if (!$this->hasIndex('vital_signs', 'vital_signs_patient_id_index')) {
                $table->index('patient_id');
            }
            if (!$this->hasIndex('vital_signs', 'vital_signs_measured_at_index')) {
                $table->index('measured_at');
            }
            if (!$this->hasIndex('vital_signs', 'vital_signs_heart_rate_index')) {
                $table->index('heart_rate');
            }
            if (!$this->hasIndex('vital_signs', 'vital_signs_systolic_bp_index')) {
                $table->index('systolic_bp');
            }
            if (!$this->hasIndex('vital_signs', 'vital_signs_created_at_index')) {
                $table->index('created_at');
            }
            // Composite index for patient and date range queries
            if (!$this->hasIndex('vital_signs', 'vital_signs_patient_id_measured_at_index')) {
                $table->index(['patient_id', 'measured_at'], 'vital_signs_patient_id_measured_at_index');
            }
        });

        // Add indexes for Alerts table
        Schema::table('alerts', function (Blueprint $table) {
            if (!$this->hasIndex('alerts', 'alerts_patient_id_index')) {
                $table->index('patient_id');
            }
            if (!$this->hasIndex('alerts', 'alerts_severity_index')) {
                $table->index('severity');
            }
            if (!$this->hasIndex('alerts', 'alerts_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('alerts', 'alerts_alert_type_index')) {
                $table->index('alert_type');
            }
            if (!$this->hasIndex('alerts', 'alerts_created_at_index')) {
                $table->index('created_at');
            }
            // Composite index for filtering active critical alerts
            if (!$this->hasIndex('alerts', 'alerts_severity_status_index')) {
                $table->index(['severity', 'status']);
            }
        });

        // Add indexes for Documents table
        Schema::table('documents', function (Blueprint $table) {
            if (!$this->hasIndex('documents', 'documents_patient_id_index')) {
                $table->index('patient_id');
            }
            if (!$this->hasIndex('documents', 'documents_doctor_id_index')) {
                $table->index('doctor_id');
            }
            if (!$this->hasIndex('documents', 'documents_file_type_index')) {
                $table->index('file_type');
            }
            if (!$this->hasIndex('documents', 'documents_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('documents', 'documents_created_at_index')) {
                $table->index('created_at');
            }
        });

        // Add indexes for ActivityLogs table (if exists)
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!$this->hasIndex('activity_logs', 'activity_logs_causer_id_index')) {
                    $table->index('causer_id');
                }
                if (!$this->hasIndex('activity_logs', 'activity_logs_subject_id_index')) {
                    $table->index('subject_id');
                }
                if (!$this->hasIndex('activity_logs', 'activity_logs_log_name_index')) {
                    $table->index('log_name');
                }
                if (!$this->hasIndex('activity_logs', 'activity_logs_created_at_index')) {
                    $table->index('created_at');
                }
                // Composite index for filtering by log type and date
                if (!$this->hasIndex('activity_logs', 'activity_logs_log_name_created_at_index')) {
                    $table->index(['log_name', 'created_at']);
                }
            });
        }

        // Add indexes for Appointments table (if exists)
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                if (!$this->hasIndex('appointments', 'appointments_patient_id_index')) {
                    $table->index('patient_id');
                }
                if (!$this->hasIndex('appointments', 'appointments_doctor_id_index')) {
                    $table->index('doctor_id');
                }
                if (!$this->hasIndex('appointments', 'appointments_status_index')) {
                    $table->index('status');
                }
                if (!$this->hasIndex('appointments', 'appointments_scheduled_at_index')) {
                    $table->index('scheduled_at');
                }
                if (!$this->hasIndex('appointments', 'appointments_created_at_index')) {
                    $table->index('created_at');
                }
                // Composite index for doctor's appointments by date
                if (!$this->hasIndex('appointments', 'appointments_doctor_id_scheduled_at_index')) {
                    $table->index(['doctor_id', 'scheduled_at'], 'appointments_doctor_id_scheduled_at_index');
                }
            });
        }

        // Add indexes for TempAccess table
        Schema::table('temp_accesses', function (Blueprint $table) {
            if (!$this->hasIndex('temp_accesses', 'temp_accesses_patient_id_index')) {
                $table->index('patient_id');
            }
            if (!$this->hasIndex('temp_accesses', 'temp_accesses_generated_by_index')) {
                $table->index('generated_by');
            }
            if (!$this->hasIndex('temp_accesses', 'temp_accesses_is_active_index')) {
                $table->index('is_active');
            }
            if (!$this->hasIndex('temp_accesses', 'temp_accesses_expires_at_index')) {
                $table->index('expires_at');
            }
            if (!$this->hasIndex('temp_accesses', 'temp_accesses_token_index')) {
                $table->index('token');
            }
            if (!$this->hasIndex('temp_accesses', 'temp_accesses_created_at_index')) {
                $table->index('created_at');
            }
            // Composite index for active sessions
            if (!$this->hasIndex('temp_accesses', 'temp_accesses_is_active_expires_at_index')) {
                $table->index(['is_active', 'expires_at']);
            }
        });

        // Add indexes for Notifications table (if exists)
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!$this->hasIndex('notifications', 'notifications_notifiable_id_index')) {
                    $table->index('notifiable_id');
                }
                if (!$this->hasIndex('notifications', 'notifications_notifiable_type_index')) {
                    $table->index('notifiable_type');
                }
                if (!$this->hasIndex('notifications', 'notifications_read_at_index')) {
                    $table->index('read_at');
                }
                if (!$this->hasIndex('notifications', 'notifications_created_at_index')) {
                    $table->index('created_at');
                }
                // Composite index for unread notifications
                if (!$this->hasIndex('notifications', 'notifications_notifiable_read_at_index')) {
                    $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
                }
            });
        }

        // Add indexes for DoctorPatient relationship table (if exists)
        if (Schema::hasTable('doctor_patients')) {
            Schema::table('doctor_patients', function (Blueprint $table) {
                if (!$this->hasIndex('doctor_patients', 'doctor_patients_doctor_id_index')) {
                    $table->index('doctor_id');
                }
                if (!$this->hasIndex('doctor_patients', 'doctor_patients_patient_id_index')) {
                    $table->index('patient_id');
                }
                if (!$this->hasIndex('doctor_patients', 'doctor_patients_status_index')) {
                    $table->index('status');
                }
                if (!$this->hasIndex('doctor_patients', 'doctor_patients_relationship_type_index')) {
                    $table->index('relationship_type');
                }
                if (!$this->hasIndex('doctor_patients', 'doctor_patients_assigned_at_index')) {
                    $table->index('assigned_at');
                }
                // Composite unique index for doctor-patient relationships
                if (!$this->hasIndex('doctor_patients', 'doctor_patients_unique_relationship')) {
                    $table->unique(['doctor_id', 'patient_id'], 'doctor_patients_unique_relationship');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for Users table
        Schema::table('users', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'users_email_index');
            $this->dropIndexIfExists($table, 'users_role_index');
            $this->dropIndexIfExists($table, 'users_status_index');
            $this->dropIndexIfExists($table, 'users_last_activity_index');
            $this->dropIndexIfExists($table, 'users_created_at_index');
            $this->dropIndexIfExists($table, 'users_name_index');
        });

        // Drop indexes for Patients table
        Schema::table('patients', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'patients_user_id_index');
            $this->dropIndexIfExists($table, 'patients_medical_record_number_index');
            $this->dropIndexIfExists($table, 'patients_date_of_birth_index');
            $this->dropIndexIfExists($table, 'patients_blood_type_index');
            $this->dropIndexIfExists($table, 'patients_created_at_index');
        });

        // Drop indexes for Doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'doctors_user_id_index');
            $this->dropIndexIfExists($table, 'doctors_license_number_index');
            $this->dropIndexIfExists($table, 'doctors_specialization_index');
            $this->dropIndexIfExists($table, 'doctors_verification_status_index');
            $this->dropIndexIfExists($table, 'doctors_created_at_index');
        });

        // Drop indexes for VitalSigns table
        Schema::table('vital_signs', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'vital_signs_patient_id_index');
            $this->dropIndexIfExists($table, 'vital_signs_measured_at_index');
            $this->dropIndexIfExists($table, 'vital_signs_heart_rate_index');
            $this->dropIndexIfExists($table, 'vital_signs_systolic_bp_index');
            $this->dropIndexIfExists($table, 'vital_signs_created_at_index');
            $this->dropIndexIfExists($table, 'vital_signs_patient_id_measured_at_index');
        });

        // Drop indexes for Alerts table
        Schema::table('alerts', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'alerts_patient_id_index');
            $this->dropIndexIfExists($table, 'alerts_severity_index');
            $this->dropIndexIfExists($table, 'alerts_status_index');
            $this->dropIndexIfExists($table, 'alerts_alert_type_index');
            $this->dropIndexIfExists($table, 'alerts_created_at_index');
            $this->dropIndexIfExists($table, 'alerts_severity_status_index');
        });

        // Drop indexes for Documents table
        Schema::table('documents', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'documents_patient_id_index');
            $this->dropIndexIfExists($table, 'documents_doctor_id_index');
            $this->dropIndexIfExists($table, 'documents_file_type_index');
            $this->dropIndexIfExists($table, 'documents_status_index');
            $this->dropIndexIfExists($table, 'documents_created_at_index');
        });

        // Drop indexes for ActivityLogs table (if exists)
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $this->dropIndexIfExists($table, 'activity_logs_causer_id_index');
                $this->dropIndexIfExists($table, 'activity_logs_subject_id_index');
                $this->dropIndexIfExists($table, 'activity_logs_log_name_index');
                $this->dropIndexIfExists($table, 'activity_logs_created_at_index');
                $this->dropIndexIfExists($table, 'activity_logs_log_name_created_at_index');
            });
        }

        // Drop indexes for Appointments table (if exists)
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                $this->dropIndexIfExists($table, 'appointments_patient_id_index');
                $this->dropIndexIfExists($table, 'appointments_doctor_id_index');
                $this->dropIndexIfExists($table, 'appointments_status_index');
                $this->dropIndexIfExists($table, 'appointments_scheduled_at_index');
                $this->dropIndexIfExists($table, 'appointments_created_at_index');
                $this->dropIndexIfExists($table, 'appointments_doctor_id_scheduled_at_index');
            });
        }

        // Drop indexes for TempAccess table
        Schema::table('temp_accesses', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'temp_accesses_patient_id_index');
            $this->dropIndexIfExists($table, 'temp_accesses_generated_by_index');
            $this->dropIndexIfExists($table, 'temp_accesses_is_active_index');
            $this->dropIndexIfExists($table, 'temp_accesses_expires_at_index');
            $this->dropIndexIfExists($table, 'temp_accesses_token_index');
            $this->dropIndexIfExists($table, 'temp_accesses_created_at_index');
            $this->dropIndexIfExists($table, 'temp_accesses_is_active_expires_at_index');
        });

        // Drop indexes for Notifications table (if exists)
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $this->dropIndexIfExists($table, 'notifications_notifiable_id_index');
                $this->dropIndexIfExists($table, 'notifications_notifiable_type_index');
                $this->dropIndexIfExists($table, 'notifications_read_at_index');
                $this->dropIndexIfExists($table, 'notifications_created_at_index');
                $this->dropIndexIfExists($table, 'notifications_notifiable_read_at_index');
            });
        }

        // Drop indexes for DoctorPatient table (if exists)
        if (Schema::hasTable('doctor_patients')) {
            Schema::table('doctor_patients', function (Blueprint $table) {
                $this->dropIndexIfExists($table, 'doctor_patients_doctor_id_index');
                $this->dropIndexIfExists($table, 'doctor_patients_patient_id_index');
                $this->dropIndexIfExists($table, 'doctor_patients_status_index');
                $this->dropIndexIfExists($table, 'doctor_patients_relationship_type_index');
                $this->dropIndexIfExists($table, 'doctor_patients_assigned_at_index');
                $this->dropIndexIfExists($table, 'doctor_patients_unique_relationship');
            });
        }
    }

    /**
     * Check if index exists on table
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        try {
            $connection = Schema::getConnection();
            $schemaBuilder = $connection->getSchemaBuilder();
            
            // Get all indexes for the table
            $indexes = $schemaBuilder->getIndexes($table);
            
            // Check if our index exists
            foreach ($indexes as $index) {
                if ($index['name'] === $indexName) {
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            // If we can't check, assume it doesn't exist
            return false;
        }
    }

    /**
     * Drop index if it exists
     */
    private function dropIndexIfExists(Blueprint $table, string $indexName): void
    {
        try {
            $table->dropIndex($indexName);
        } catch (Exception $e) {
            // Index doesn't exist, ignore
        }
    }
};
