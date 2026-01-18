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
        Schema::create('read_taxs_and_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_and_app_id')->constrained('taxs_and_apps');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('id_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('read_taxs_and_apps');
    }
};
