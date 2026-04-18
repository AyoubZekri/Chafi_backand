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
        Schema::create('law_taxs_and_app', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_fr');
            $table->foreignId('law_id')->nullable()->constrained('laws')->onDelete('set null');
            $table->foreignId('taxs_and_app_id')->constrained('taxs_and_apps')->onDelete('cascade');
            $table->string('index_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_taxs_and_app');
    }
};
