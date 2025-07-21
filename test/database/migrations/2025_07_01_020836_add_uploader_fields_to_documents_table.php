<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('uploader_name')->nullable()->after('user_id');
            $table->string('uploader_hospital')->nullable()->after('uploader_name');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['uploader_name', 'uploader_hospital']);
        });
    }
};
