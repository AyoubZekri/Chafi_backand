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
        // محادثات المساعد الذكي لكل مستخدم
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->timestamps();

            $table->index(['user_id', 'updated_at']);
        });

        // رسائل كل محادثة: role = user / assistant
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->string('role', 20);
            $table->longText('content');
            // المصادر (مواد/أنظمة/مؤسسات/جزاءات) والعمليات الحسابية لرسائل المساعد
            $table->json('sources')->nullable();
            $table->json('calculations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};
