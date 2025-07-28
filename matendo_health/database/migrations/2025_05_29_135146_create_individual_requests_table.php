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
        Schema::create('individual_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            
            // Personal Information
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            
            // Care Requirements
            $table->string('care_type')->nullable();
            $table->text('care_requirements')->nullable();
            $table->json('schedule')->nullable(); // Store as JSON array
            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();
            $table->text('allergies')->nullable(); // Added field
            
            // Emergency Contact
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();

            // Job Description (if applicable)
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();

            // Document Storage (File path instead of LONGBLOB)
            $table->string('job_description_file_path')->nullable();
            $table->string('job_description_file_name')->nullable();
            $table->string('job_description_file_type')->nullable();

            // Processing Information
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            
            // CSRF protection
            $table->string('csrf_token');
            
            $table->timestamps();
            $table->softDeletes(); // For rejected requests

            // Foreign key constraints
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('email');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_requests');
    }
};