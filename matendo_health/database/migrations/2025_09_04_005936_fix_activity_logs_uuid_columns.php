<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify columns directly
        DB::statement('ALTER TABLE activity_logs MODIFY causer_id CHAR(36) NULL');
        DB::statement('ALTER TABLE activity_logs MODIFY subject_id CHAR(36) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('activity_logs_causer_index');
            $table->dropIndex('activity_logs_subject_index');
            
            // Change back to integer (for standard morphs)
            $table->unsignedBigInteger('causer_id')->nullable()->change();
            $table->unsignedBigInteger('subject_id')->nullable()->change();
            
            // Re-add original indexes
            $table->index(['causer_type', 'causer_id']);
            $table->index(['subject_type', 'subject_id']);
        });
    }
};
