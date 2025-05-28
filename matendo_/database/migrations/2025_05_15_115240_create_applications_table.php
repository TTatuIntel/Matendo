<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    public function up()
    {
       /**
     * Run the migrations.
     */
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
           // Unique tracking reference
            $table->string('reference_number')->unique();

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('location')->nullable();
            $table->string('coordinates')->nullable();

            // Professional Information
            $table->string('profession');
            $table->string('other_profession')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('years_experience')->default(0);
            $table->string('license_number')->nullable();
            $table->string('resume')->nullable();
            $table->string('license_doc')->nullable();
            $table->string('certifications')->nullable();

            // Work Preferences
            $table->json('work_type')->nullable();
            $table->json('shift_type')->nullable();
            $table->string('preferred_location')->nullable();
            $table->date('start_date')->nullable();

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
        Schema::dropIfExists('applications');
    }
};