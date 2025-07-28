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
            $table->text('address');
            
            // Care Details
            $table->string('care_type');
            $table->text('care_requirements')->nullable();
            $table->json('schedule')->nullable(); // JSON for multi-select
            
            // Medical Information
            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact');
            $table->string('emergency_phone');
            
            // Job Requirements
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->longText('job_description')->nullable();
            
            // Document Storage (Base64 storage for consistency with join.php)
            $table->longText('job_description_base64')->nullable();
            $table->string('job_description_name')->nullable();
            $table->string('job_description_mime')->nullable();
            $table->integer('job_description_size')->nullable();
            
            // Processing Information
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('confirmed')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            
            // System Fields (consistent with join.php)
            $table->string('csrf_token')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('form_metadata')->nullable(); // JSON string for consistency
            
            $table->timestamps();
            $table->softDeletes(); // For rejected requests
            
            // Foreign key constraints
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('email');
            $table->index('reference_number');
            $table->index('care_type');
            $table->index('priority');
            $table->index('full_name');
            $table->index('processed_at');
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