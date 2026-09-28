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
        Schema::table('users', function (Blueprint $table) {
            $table->string('is_taxpayer')->nullable()->comment('المكلف بالضريبة (نوع أو وصف)');
            $table->boolean('is_registered_tax_admin')->nullable()->comment('هل مسجل في الإدارة الجبائية');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_taxpayer', 'is_registered_tax_admin']);
        });
    }
};
