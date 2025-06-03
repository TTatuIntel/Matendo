<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            // Drop old FK first
            $table->dropForeign(['assigned_to']);

            // Then re-add with correct reference
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);

            // Restore original FK (to applications)
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('applications')
                  ->onDelete('cascade');
        });
    }
};
