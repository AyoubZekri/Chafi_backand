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
        Schema::create('activitys', function (Blueprint $table) {
            $table->id();
            $table->integer('index');
            $table->foreignId('nataire_activitys_id')->constrained('nataire_activitys');
            $table->string('name');
            $table->text('body');
            $table->string('name_fr');
            $table->text('body_fr');
            $table->foreignId('tax_id')->nullable();
            $table->integer('status_tax')->default(0);
            $table->integer('code_activity')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activitys');
    }
};
