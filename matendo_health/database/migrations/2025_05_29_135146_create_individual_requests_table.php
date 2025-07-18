<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndividualRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('individual_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('care_type');
            $table->text('care_requirements')->nullable();
            $table->string('schedule')->nullable();
            $table->string('medical_conditions')->nullable();
            $table->string('medications')->nullable();
            $table->string('emergency_contact');
            $table->string('emergency_phone');
            $table->string('reference_number')->unique();
            $table->timestamp('submission_date')->nullable();
            $table->string('csrf_token');
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('job_description')->nullable();
            $table->string('job_description_file')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('individual_requests');
    }
}
