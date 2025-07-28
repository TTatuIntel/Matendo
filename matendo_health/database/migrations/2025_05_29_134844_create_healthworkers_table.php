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
        Schema::create('healthworkers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('application_id')->unique();
           
            // Personal Information (duplicated from application for quick access)
            $table->string('name');
            $table->string('email')->nullable(); // ✅ Fixed: Made nullable
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
           
            // Professional Information
            $table->string('profession')->nullable();
            $table->string('specialization')->nullable();
            $table->string('specialty')->nullable(); // Alias for specialization
            $table->integer('years_experience')->nullable();
            $table->string('license_number')->nullable();
           
            // Document References (paths to stored files)
            $table->string('resume_path')->nullable();
            $table->string('license_path')->nullable();
            $table->string('certifications_path')->nullable();
            $table->string('application_snapshot_path')->nullable();
           
            // Work Preferences
            $table->json('work_type')->nullable();
            $table->json('shift_type')->nullable();
            $table->string('preferred_location')->nullable();
            $table->date('available_from')->nullable();
           
            // Status and Verification
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->boolean('verified')->default(true); // Auto-verified when approved
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
           
            // Performance tracking
            $table->integer('tasks_completed')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('total_ratings')->default(0);
           
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
           
            // Indexes
            $table->index(['status', 'verified']);
            $table->index('specialization');
            $table->index('profession');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('healthworkers');
    }
};