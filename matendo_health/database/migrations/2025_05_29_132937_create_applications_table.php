<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('location')->nullable();
            $table->string('coordinates')->nullable();
            $table->string('profession');
            $table->string('other_profession')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('years_experience')->nullable();
            $table->string('license_number')->nullable();

            $table->string('resume_name')->nullable();
            $table->string('resume_type')->nullable();
            $table->string('license_name')->nullable();
            $table->string('license_type')->nullable();
            $table->string('certifications_name')->nullable();
            $table->string('certifications_type')->nullable();

            $table->json('work_type')->nullable();
            $table->json('shift_type')->nullable();
            $table->string('preferred_location')->nullable();
            $table->date('start_date')->nullable();

            $table->string('status')->default('pending');
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
        });

        // LONGBLOBs added after
        DB::statement("ALTER TABLE applications ADD resume LONGBLOB NULL");
        DB::statement("ALTER TABLE applications ADD license_doc LONGBLOB NULL");
        DB::statement("ALTER TABLE applications ADD certifications LONGBLOB NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
