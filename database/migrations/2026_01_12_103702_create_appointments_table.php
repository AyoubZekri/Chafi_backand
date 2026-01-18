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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('index');
            $table->foreignId('tax_id')->nullable();
            $table->string('declaration');
            $table->string('declaration_fr');
            $table->date('deadline')->nullable();
            $table->binary('dependencies')->nullable();
            $table->binary('dependencies_fr')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
