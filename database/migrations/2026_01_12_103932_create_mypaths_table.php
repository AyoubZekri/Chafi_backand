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
        Schema::create('mypaths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate("cascade");
            $table->integer('person_type');
            $table->foreignId('nataire_activity_id')->nullable()->constrained('nataire_activitys')->onDelete('set null')->onUpdate('set null');
            $table->foreignId('activity_id')->nullable()->constrained('activitys')->onDelete('set null')->onUpdate('set null');
            $table->foreignId('tax_id')->nullable();
            $table->tinyInteger('activit_special')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mypaths');
    }
};
