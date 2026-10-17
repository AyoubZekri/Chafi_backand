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
        // مواضيع مقترحة عندما لا يفهم المساعد السؤال (يختار المستخدم أحدها)
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->json('options')->nullable()->after('calculations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
};
