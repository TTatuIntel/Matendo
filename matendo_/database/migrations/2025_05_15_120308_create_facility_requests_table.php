<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facility_requests', function (Blueprint $table) {
            $table->id();
            $table->string('facility_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->string('coordinates')->nullable();
            $table->string('facility_type');
            $table->string('positions');
            $table->string('employment_type');
            $table->string('shift_type');
            $table->integer('staff_number');
            $table->date('start_date');
            $table->string('job_requirement_option')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();
            $table->string('job_description_file')->nullable();
            $table->string('reference_number');
            $table->timestamp('submission_date');
            $table->string('csrf_token');
            // Status and confirmation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->timestamps(); // This adds `created_at` and `updated_at` columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_requests');
    }
}
