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
        Schema::table('doctor_patients', function (Blueprint $table) {
            // Add removed tracking fields if they do not exist
            if (!Schema::hasColumn('doctor_patients', 'removed_at')) {
                $table->timestamp('removed_at')->nullable()->after('assigned_at');
            }
            if (!Schema::hasColumn('doctor_patients', 'removed_by')) {
                $table->uuid('removed_by')->nullable()->after('removed_at');
            }
            // Ensure notes column exists (used in PatientManagementController assignDoctor)
            if (!Schema::hasColumn('doctor_patients', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            // Optional FK for removed_by
            // Note: Some databases require separate statements for adding foreign keys conditionally
            try {
                $table->foreign('removed_by')->references('id')->on('users')->onDelete('set null');
            } catch (\Throwable $e) {
                // Ignore if FK already exists or cannot be added conditionally
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_patients', function (Blueprint $table) {
            // Drop foreign key first if exists
            try {
                $table->dropForeign(['removed_by']);
            } catch (\Throwable $e) {
                // ignore
            }
            if (Schema::hasColumn('doctor_patients', 'removed_by')) {
                $table->dropColumn('removed_by');
            }
            if (Schema::hasColumn('doctor_patients', 'removed_at')) {
                $table->dropColumn('removed_at');
            }
            if (Schema::hasColumn('doctor_patients', 'notes')) {
                // Only drop if we created it here (cannot easily check provenance). We'll keep it to avoid data loss.
                // $table->dropColumn('notes');
            }
        });
    }
};
