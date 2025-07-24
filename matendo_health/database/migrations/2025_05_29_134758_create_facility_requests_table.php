<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_requests', function (Blueprint $table) {
            $table->id();
            $table->string('facility_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->string('coordinates')->nullable();
            $table->string('facility_type')->nullable();
            $table->string('positions')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('shift_type')->nullable();
            $table->integer('staff_number')->nullable();
            $table->date('start_date')->nullable();

            $table->string('job_requirement_option')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();

            $table->string('job_description_file_name')->nullable();
            $table->string('job_description_file_type')->nullable();

            $table->string('reference_number')->unique();
            $table->string('csrf_token');
            $table->string('status')->default('pending');
            $table->string('priority')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE facility_requests ADD job_description_file LONGBLOB NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_requests');
    }
};
