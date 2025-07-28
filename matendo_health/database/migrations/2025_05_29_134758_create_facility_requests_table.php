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
        Schema::create('facility_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            
            // Facility Information
            $table->string('facility_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->string('coordinates')->nullable();
            
            // Request Details (JSON for multi-select fields)
            $table->json('facility_type')->nullable();
            $table->string('other_facility_type')->nullable();
            $table->json('positions')->nullable();
            $table->string('other_position')->nullable();
            $table->json('employment_type')->nullable(); // duration field mapped here
            $table->json('shift_type')->nullable();
            $table->integer('staff_number')->nullable();
            $table->date('start_date')->nullable();

            // Job Description
            $table->string('job_requirement_option')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();

            // Document Storage (File path instead of LONGBLOB)
            $table->string('job_description_file_path')->nullable();
            $table->string('job_description_file_name')->nullable();
            $table->string('job_description_file_type')->nullable();

            // Processing Information
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
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
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_requests');
    }
};