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
        Schema::create('doctor_patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('doctor_id');
            $table->uuid('patient_id');
            $table->enum('relationship_type', ['primary', 'consulting', 'specialist', 'referral'])->default('primary');
            $table->enum('status', ['active', 'inactive', 'transferred', 'discharged'])->default('active');
            $table->timestamp('assigned_at')->useCurrent();
            $table->uuid('assigned_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['doctor_id', 'patient_id', 'relationship_type'], 'unique_doctor_patient_relationship');
            
            // Indexes for performance
            $table->index(['doctor_id', 'status']);
            $table->index(['patient_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_patients');
    }
};
