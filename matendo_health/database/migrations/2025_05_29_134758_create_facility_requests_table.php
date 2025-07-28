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
            $table->text('coordinates')->nullable();
           
            // Request Details (JSON for multi-select fields)
            $table->json('facility_type')->nullable();
            $table->string('other_facility_type')->nullable();
            $table->json('positions')->nullable();
            $table->string('other_position')->nullable();
            $table->json('employment_type')->nullable(); // duration field mapped here
            $table->json('shift_type')->nullable();
            $table->integer('staff_number')->default(0);
            $table->date('start_date')->nullable();
            
            // Job Description
            $table->string('job_requirement_option')->nullable();
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
            $table->index('priority');
            $table->index('facility_name');
            $table->index('processed_at');
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