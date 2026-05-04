<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stats', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullable()->onDelete('cascade')->onUpdate("cascade");
            $table->tinyInteger('type_stats')->default(0);
            // 1 ins
            // 2 tax
            // 3 cart
            // 4 Clalcelater
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stats', function (Blueprint $table) {
            $table->dropColumn('type_stats');
            $table->dropColumn('user_id');
        });
    }
};
