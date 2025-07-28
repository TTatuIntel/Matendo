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
        // Only create documents table if it doesn't exist
        if (!Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('filename');
                $table->string('path');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size'); // Size in bytes
                $table->string('category')->nullable(); // Optional category
                $table->string('uploader_name')->nullable();
                $table->string('uploader_hospital')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'category']);
            });
        } else {
            // Add missing columns to existing documents table
            Schema::table('documents', function (Blueprint $table) {
                if (!Schema::hasColumn('documents', 'mime_type')) {
                    $table->string('mime_type')->nullable()->after('category');
                }
                if (!Schema::hasColumn('documents', 'uploader_name')) {
                    $table->string('uploader_name')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('documents', 'uploader_hospital')) {
                    $table->string('uploader_hospital')->nullable()->after('uploader_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (Schema::hasTable('documents')) {
            Schema::dropIfExists('documents');
        }
    }
};