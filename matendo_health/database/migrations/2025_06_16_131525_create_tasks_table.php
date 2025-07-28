<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
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
            $table->string('reference_number')->unique();
            $table->timestamp('submission_date')->useCurrent()->useCurrentOnUpdate();
            $table->string('csrf_token');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->string('priority')->nullable();
            $table->boolean('confirmed')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
