<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'medical_conditions')) {
                $table->string('medical_conditions')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'medications')) {
                $table->string('medications')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable();
            }
            // $table->string('medical_conditions')->nullable();
            // $table->string('medications')->nullable();
            // $table->string('emergency_contact')->nullable();
            // $table->string('emergency_phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['medical_conditions', 'medications', 'emergency_contact', 'emergency_phone']);
        });
    }
}
