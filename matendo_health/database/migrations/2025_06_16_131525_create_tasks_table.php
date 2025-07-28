<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Optional reference to related request
            $table->enum('source_type', ['facility_request', 'individual_request', 'manual'])->default('manual');
            $table->unsignedBigInteger('source_id')->nullable();

            // Task core info
            $table->string('reference_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('coordinates')->nullable();

            // Staffing and skills
            $table->json('required_skills')->nullable();
            $table->integer('staff_needed')->default(1);

            // Timing
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Assignment
            $table->unsignedBigInteger('assigned_to')->nullable();

            // Status and priority
            $table->enum('status', ['open', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Contact information
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // Rating and feedback
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('assigned_to')->references('id')->on('healthworkers')->onDelete('set null');

            // Indexes
            $table->index(['status', 'start_date']);
            $table->index(['source_type', 'source_id']);
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
