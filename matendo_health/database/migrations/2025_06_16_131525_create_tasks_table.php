<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Source tracking
            $table->enum('source_type', ['facility_request', 'individual_request', 'manual'])->default('manual');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('reference_number')->unique();

            // Common fields for all task types
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('coordinates')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'In Progress', 'Completed'])->default('Pending');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->boolean('confirmed')->default(false);
            $table->boolean('complete')->default(false);

            // Facility-specific fields (from facility_requests table)
            $table->string('facility_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('facility_type')->nullable();
            $table->string('other_facility_type')->nullable();
            $table->json('positions')->nullable();
            $table->string('other_position')->nullable();
            $table->json('employment_type')->nullable();
            $table->json('shift_type')->nullable();
            $table->integer('staff_number')->default(1);
            $table->string('job_requirement_option')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->longText('job_description')->nullable();

            // Individual-specific fields (from individual_requests table)
            $table->string('full_name')->nullable();
            $table->text('address')->nullable();
            $table->string('care_type')->nullable();
            $table->text('care_requirements')->nullable();
            $table->json('schedule')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();

            // Document storage
            $table->longText('job_description_base64')->nullable();
            $table->string('job_description_name')->nullable();
            $table->string('job_description_mime')->nullable();
            $table->integer('job_description_size')->nullable();
            $table->string('job_description_file')->nullable();

            // Assignment and tracking
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('assigned_to_name')->nullable(); // Store name for display
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();

            // System fields
            $table->string('csrf_token')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('form_metadata')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submission_date')->nullable();

            // Legacy fields that might be referenced
            $table->json('required_skills')->nullable();
            $table->integer('staff_needed')->default(1);
            $table->string('location')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->string('urgency')->nullable(); // Maps to priority for display

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['status', 'start_date']);
            $table->index(['source_type', 'source_id']);
            $table->index('priority');
            $table->index('reference_number');
            $table->index('email');
            $table->index('facility_name');
            $table->index('full_name');
            $table->index('assigned_to');
            $table->index('processed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};