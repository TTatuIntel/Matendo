<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $admin = User::firstOrCreate([
            'email' => 'admin@mcares.com'
        ], [
            'id' => Str::uuid(),
            'name' => 'System Administrator',
            'email' => 'admin@mcares.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '+1-555-0101',
            'date_of_birth' => '1985-01-01',
            'gender' => 'other',
            'address' => '123 Medical Center Drive, Health City, HC 12345',
            'emergency_contact_name' => 'Emergency Services',
            'emergency_contact_phone' => '+1-555-0911',
            'email_verified_at' => now(),
            'last_activity' => now(),
        ]);

        // Create Sample Doctor User
        $doctor = User::firstOrCreate([
            'email' => 'doctor@mcares.com'
        ], [
            'id' => Str::uuid(),
            'name' => 'Dr. John Smith',
            'email' => 'doctor@mcares.com',
            'password' => Hash::make('doctor123'),
            'role' => 'doctor',
            'status' => 'active',
            'phone' => '+1-555-0102',
            'date_of_birth' => '1980-06-15',
            'gender' => 'male',
            'address' => '456 Medical Plaza, Health City, HC 12346',
            'emergency_contact_name' => 'Dr. Emergency Contact',
            'emergency_contact_phone' => '+1-555-0912',
            'email_verified_at' => now(),
            'last_activity' => now(),
        ]);

        // Create Doctor Profile
        if ($doctor->wasRecentlyCreated) {
            Doctor::create([
                'id' => Str::uuid(),
                'user_id' => $doctor->id,
                'license_number' => 'MD-' . strtoupper(Str::random(8)),
                'specialization' => 'Internal Medicine',
                'qualifications' => 'MD, Board Certified Internal Medicine',
                'hospital_affiliation' => 'MCares General Hospital',
                'years_experience' => 15,
                'bio' => 'Experienced internal medicine physician specializing in comprehensive patient care.',
                'available_hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17],
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verified_by' => $admin->id,
                'consultation_fee' => 150.00,
                'accepts_emergency_calls' => true,
            ]);
        }

        // Create Sample Patient User
        $patient = User::firstOrCreate([
            'email' => 'patient@mcares.com'
        ], [
            'id' => Str::uuid(),
            'name' => 'Jane Doe',
            'email' => 'patient@mcares.com',
            'password' => Hash::make('patient123'),
            'role' => 'patient',
            'status' => 'active',
            'phone' => '+1-555-0103',
            'date_of_birth' => '1990-03-20',
            'gender' => 'female',
            'address' => '789 Residential Street, Health City, HC 12347',
            'emergency_contact_name' => 'John Doe (Spouse)',
            'emergency_contact_phone' => '+1-555-0913',
            'email_verified_at' => now(),
            'last_activity' => now(),
        ]);

        // Create Patient Profile
        if ($patient->wasRecentlyCreated) {
            Patient::create([
                'id' => Str::uuid(),
                'user_id' => $patient->id,
                'medical_record_number' => 'MRN-' . strtoupper(Str::random(10)),
                'blood_type' => 'O+',
                'height' => 165.00,
                'current_weight' => 65.00,
                'allergies' => 'Penicillin, Shellfish',
                'chronic_conditions' => 'None',
                'current_medications' => 'Multivitamins',
                'insurance_provider' => 'HealthCare Plus',
                'insurance_policy_number' => 'HCP-' . Str::random(8),
                'family_medical_history' => 'Diabetes (maternal), Hypertension (paternal)',
                'activity_level' => 'moderate',
                'smoker' => false,
                'alcohol_consumption' => 1,
                'dietary_restrictions' => 'Vegetarian',
                'emergency_contacts' => [
                    [
                        'name' => 'John Doe',
                        'relationship' => 'Spouse',
                        'phone' => '+1-555-0913',
                        'email' => 'john.doe@email.com'
                    ]
                ],
                'baseline_heart_rate' => 70.0,
                'baseline_blood_pressure' => '120/80',
                'baseline_temperature' => 98.6,
            ]);
        }

        // Create Additional Admin User for Testing
        $adminTest = User::firstOrCreate([
            'email' => 'test.admin@mcares.com'
        ], [
            'id' => Str::uuid(),
            'name' => 'Test Admin',
            'email' => 'test.admin@mcares.com',
            'password' => Hash::make('test123'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '+1-555-0104',
            'date_of_birth' => '1988-05-10',
            'gender' => 'other',
            'address' => '321 Test Street, Health City, HC 12348',
            'emergency_contact_name' => 'Emergency Services',
            'emergency_contact_phone' => '+1-555-0911',
            'email_verified_at' => now(),
            'last_activity' => now(),
        ]);

        $this->command->info('Admin users created successfully:');
        $this->command->info('- System Administrator: admin@mcares.com / admin123');
        $this->command->info('- Test Admin: test.admin@mcares.com / test123');
        $this->command->info('- Sample Doctor: doctor@mcares.com / doctor123');
        $this->command->info('- Sample Patient: patient@mcares.com / patient123');
    }
}
