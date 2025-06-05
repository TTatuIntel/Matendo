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
    Schema::table('users', function (Blueprint $table) {
        $table->string('task1')->nullable();
        $table->string('task2')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['task1', 'task2']);
    });
}
};
