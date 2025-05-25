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
            $table->string('facility_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->text('location')->nullable();
            $table->string('coordinates')->nullable();

            // Facility & position details
            $table->json('facility_types');
            $table->string('other_facility_type')->nullable();
            $table->json('positions_needed');
            $table->string('other_position')->nullable();

            // Employment details
            $table->json('employment_types');
            $table->json('shift_types');
            $table->integer('staff_number');
            $table->date('start_date')->nullable();

            // Job requirement details
            $table->enum('requirement_option', ['none', 'upload', 'manual'])->default('none');
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();
            $table->string('job_description_file')->nullable(); // Single file upload

            // Status and confirmation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
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
