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
        if (!Schema::hasTable('temp_access_documents')) {
            Schema::create('temp_access_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('granted_by_id')->constrained('users')->onDelete('cascade');
                $table->string('filename');
                $table->string('path');
                $table->string('category')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'granted_by_id']);
                $table->index('expires_at');
            });
        } else {
            // Add missing columns to existing table
            Schema::table('temp_access_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('temp_access_documents', 'mime_type')) {
                    $table->string('mime_type')->nullable()->after('category');
                }
                if (!Schema::hasColumn('temp_access_documents', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('size');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_access_documents');
    }
};