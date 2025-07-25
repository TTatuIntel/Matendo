<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration

{
    /**
     * Run the applications migrations.
     *
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone');
            $table->string('address');
            $table->string('location')->nullable();
            $table->string('coordinates')->nullable();
            $table->string('profession');
            $table->string('other_profession')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('years_experience')->default(0);
            $table->string('license_number')->nullable();
            $table->string('resume')->nullable();
            $table->string('license_doc')->nullable();
            $table->string('certifications')->nullable();
            $table->longText('work_type')->nullable();
            $table->longText('shift_type')->nullable();
            $table->string('preferred_location')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
              $table->softDeletes(); // This adds the deleted_at column

        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
