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
        // Ensure a clean state if a partial table exists from a previous failed migration
        if (Schema::hasTable('alerts')) {
            Schema::drop('alerts');
        }

        Schema::create('alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('triggered_by')->nullable();
            $table->string('alert_type');
            $table->enum('severity', ['info', 'warning', 'critical', 'emergency'])->default('info');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->json('trigger_conditions')->nullable();
            $table->boolean('auto_generated')->default(false);
            $table->timestamp('triggered_at');
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'false_alarm'])->default('active');
            $table->foreignUuid('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('acknowledgment_notes')->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->json('notifications_sent')->nullable();
            $table->boolean('requires_doctor_attention')->default(false);
            $table->boolean('requires_emergency_contact')->default(false);
            $table->json('escalation_rules')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'severity']);
            $table->index(['status', 'severity']);
            $table->index(['alert_type']);
            $table->index(['triggered_at']);
            $table->index(['requires_doctor_attention']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
