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
        // Update the enum to include 'secondary' as an allowed value
        DB::statement("ALTER TABLE doctor_patients MODIFY COLUMN relationship_type ENUM('primary', 'secondary', 'consulting', 'specialist', 'referral') NOT NULL DEFAULT 'primary'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum values
        DB::statement("ALTER TABLE doctor_patients MODIFY COLUMN relationship_type ENUM('primary', 'consulting', 'specialist', 'referral') NOT NULL DEFAULT 'primary'");
    }
};
