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
        Schema::create('health_workers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained()->onDelete('cascade');

            // Copied fields from the application
            $table->string('name');
            $table->string('specialty')->nullable();

            // Documents originally uploaded in the application
            $table->string('resume')->nullable();              // PDF or Word
            $table->string('license_doc')->nullable();         // PDF/image
            $table->string('certifications')->nullable();      // PDF/image or zip

            // Application snapshot (PDF)
            $table->string('application_snapshot_pdf')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_workers'); 
    }
};
