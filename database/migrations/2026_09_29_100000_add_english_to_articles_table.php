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
        // النسخة الإنجليزية لتسمية المادة ونصها (يعرضها التطبيق عند اختيار اللغة الإنجليزية)
        Schema::table('articles', function (Blueprint $table) {
            $table->string('label_en', 255)->nullable()->after('label');
            $table->mediumText('text_en')->nullable()->after('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['label_en', 'text_en']);
        });
    }
};
