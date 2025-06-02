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
 Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('task_type'); // 'facility_booking' or 'individual_request'
            $table->unsignedBigInteger('task_id'); // ID of the facility booking or individual request
            $table->unsignedBigInteger('assignee_id'); // ID of the person assigned
            $table->unsignedBigInteger('assigned_by'); // ID of admin who assigned
            $table->timestamp('assigned_at');
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['task_type', 'task_id']);
            $table->foreign('assigned_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
