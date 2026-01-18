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
            $table->foreignId('user_id')->constrained('users');
            $table->integer('person_type');
            $table->foreignId('nataire_activity_id')->constrained('nataire_activitys');
            $table->foreignId('activity_id')->constrained('activitys');
            $table->foreignId('tax_id')->nullable();
            $table->string('activit_special')->nullable();
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
