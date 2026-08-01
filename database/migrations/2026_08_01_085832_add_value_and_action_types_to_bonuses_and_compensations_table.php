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
        Schema::table('bonuses_and_compensations', function (Blueprint $table) {
            $table->tinyInteger('value_type')->nullable()->comment('1: نسبة, 2: مبلغ')->after('type');
            $table->tinyInteger('action_type')->nullable()->comment('1: زيادة, 2: اقتطاع')->after('value_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonuses_and_compensations', function (Blueprint $table) {
            $table->dropColumn(['value_type', 'action_type']);
        });
    }
};
