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
        // الملف المرفوع (PDF/Word) الخاص بالوثيقة، مخزن في القرص public
        // (source_file يبقى لاسم الملف المصدر الذي يستعمله أمر الاستيراد)
        Schema::table('documents', function (Blueprint $table) {
            $table->string('file', 255)->nullable()->after('source_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('file');
        });
    }
};
