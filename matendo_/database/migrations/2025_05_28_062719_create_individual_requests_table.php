<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('individual_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('individual_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->text('location')->nullable();
            $table->string('coordinates')->nullable();
            $table->longText('request_types');
            $table->string('other_request_type')->nullable();
            $table->longText('skills_needed');
            $table->string('other_skill')->nullable();
            $table->longText('employment_types');
            $table->longText('shift_types');
            $table->integer('staff_number');
            $table->date('start_date')->nullable();
            $table->enum('requirement_option', ['none', 'upload', 'manual']);
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();
            $table->string('job_description_file')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('confirmed')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('individual_requests');
    }
};
