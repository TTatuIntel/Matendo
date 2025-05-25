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
        Schema::create('personal_care_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('city');
            $table->string('postal_code')->nullable();

            $table->string('care_type');
            $table->string('other_care_type')->nullable();
            $table->text('care_requirements');
            $table->json('schedule')->nullable();

            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();
            $table->text('allergies')->nullable();
            $table->string('emergency_contact');
            $table->string('emergency_phone');

            $table->string('care_file')->nullable(); // Single file upload

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
        Schema::dropIfExists('personal_care_requests');
    }
};
