<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('task_assignments', function (Blueprint $table) {
        $table->id();
        $table->string('task_type'); // 'facility' or 'individual'
        $table->unsignedBigInteger('task_id'); // ID from facility_requests or individual_requests
        $table->unsignedBigInteger('assigned_to'); // ID from applications table
        $table->timestamps();

        $table->foreign('assigned_to')->references('id')->on('applications')->onDelete('cascade');
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
