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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            
            // Application identification
            $table->string('reference_number')->unique();
            
            // Personal information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('location')->nullable();
            $table->string('coordinates')->nullable();
            
            // Professional information
            $table->string('profession');
            $table->string('other_profession')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('years_experience')->default(0);
            $table->string('license_number')->nullable();
            
            // Document storage (base64 encoded)
            $table->longText('resume_base64')->nullable();
            $table->string('resume_name')->nullable();
            $table->string('resume_mime')->nullable();
            $table->integer('resume_size')->nullable();
            
            $table->longText('license_base64')->nullable();
            $table->string('license_name')->nullable();
            $table->string('license_mime')->nullable();
            $table->integer('license_size')->nullable();
            
            $table->longText('certifications_base64')->nullable();
            $table->string('certifications_name')->nullable();
            $table->string('certifications_mime')->nullable();
            $table->integer('certifications_size')->nullable();
            
            // Work preferences (stored as JSON)
            $table->json('work_type')->nullable();
            $table->json('shift_type')->nullable();
            $table->string('preferred_location')->nullable();
            $table->date('start_date')->nullable();
            
            // Status and processing
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Metadata
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('form_metadata')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['email']);
            $table->index(['profession']);
            $table->index(['processed_at']);
            $table->index(['reference_number']);
            
            // Foreign key constraint
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};