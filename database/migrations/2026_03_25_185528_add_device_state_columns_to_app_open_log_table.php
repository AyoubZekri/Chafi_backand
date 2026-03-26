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
        Schema::table('stats', function (Blueprint $table) {
            $table->string('device_id')->after('id');
            $table->string('state')->after('device_id');
            $table->integer('type_user')->nullable(); //1 user //2Guest
            $table->date('open_date')->after('state');
            $table->unique(['device_id', 'open_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_open_log', function (Blueprint $table) {
            $table->dropUnique(['device_id', 'open_date']);
            $table->dropColumn(['device_id', 'state', 'open_date']);
        });
    }
};
