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
        Schema::create('approved', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('application_id');
            $table->string('reference_code');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profession');
            $table->timestamp('approved_at')->nullable();

            // Foreign key constraint
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved');
    }
};
