<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthworkersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('healthworkers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('application_id');
            $table->string('name');
            $table->string('specialty')->nullable();
            $table->string('resume')->nullable();
            $table->string('license_doc')->nullable();
            $table->string('certifications')->nullable();
            $table->string('application_snapshot_pdf')->nullable();

            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Optional: Add foreign keys if needed, example:
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('healthworkers');
    }
}
