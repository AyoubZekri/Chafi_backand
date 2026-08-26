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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('read_time')->nullable();
            $table->text('chafi_advice')->nullable();
            $table->text('chafi_advice_fr')->nullable();
            $table->text('legal_source')->nullable();
            $table->text('legal_source_fr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'read_time',
                'chafi_advice',
                'chafi_advice_fr',
                'legal_source',
                'legal_source_fr',
            ]);
        });
    }
};
