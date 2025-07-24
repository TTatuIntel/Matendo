<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('individual_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('care_type')->nullable();
            $table->text('care_requirements')->nullable();
            $table->string('schedule')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();

            $table->string('reference_number')->unique();
            $table->string('csrf_token');
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();

            $table->string('job_description_file_name')->nullable();
            $table->string('job_description_file_type')->nullable();

            $table->string('status')->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE individual_requests ADD job_description_file LONGBLOB NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('individual_requests');
    }
};
