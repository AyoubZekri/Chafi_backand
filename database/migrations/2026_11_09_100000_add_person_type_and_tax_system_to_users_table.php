<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('person_type', 20)->nullable()
                ->after('is_registered_tax_admin')
                ->comment('طبيعة الشخص للمسجل في الإدارة الجبائية: natural / legal');
            $table->string('tax_system', 20)->nullable()
                ->after('person_type')
                ->comment('النظام الجبائي للمسجل: real / flat_rate / simplified');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['person_type', 'tax_system']);
        });
    }
};
