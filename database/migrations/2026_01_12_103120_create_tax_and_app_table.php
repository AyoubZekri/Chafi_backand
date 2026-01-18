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
        Schema::create('taxs_and_apps', function (Blueprint $table) {
            $table->id();
            $table->integer('index');
            $table->foreignId('cat_id')->constrained('categories');
            $table->string('title');
            $table->text('body');
            $table->string('title_fr');
            $table->text('body_fr');
            $table->foreignId('law_id')->constrained('laws')->nullable()->default(null);
            $table->string('index_link')->nullable();
            $table->string('calcul')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxs_and_apps');
    }
};
