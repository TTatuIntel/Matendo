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
        Schema::table('documents', function (Blueprint $table) {
            // Add missing columns for document management
            $table->integer('external_access_count')->default(0)->after('is_confidential');
            $table->string('document_type', 100)->nullable()->after('file_type');
            $table->timestamp('last_accessed_at')->nullable()->after('updated_at');
            $table->char('last_accessed_by', 36)->nullable()->after('last_accessed_at');
            
            // Add indexes for performance
            $table->index('external_access_count');
            $table->index('document_type');
            $table->index('last_accessed_at');
            $table->index('last_accessed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['external_access_count']);
            $table->dropIndex(['document_type']);
            $table->dropIndex(['last_accessed_at']);
            $table->dropIndex(['last_accessed_by']);
            
            $table->dropColumn([
                'external_access_count',
                'document_type',
                'last_accessed_at',
                'last_accessed_by'
            ]);
        });
    }
};
