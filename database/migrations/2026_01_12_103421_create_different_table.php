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
        Schema::create('differents', function (Blueprint $table) {
            $table->id();
            $table->integer('index');
            $table->integer('type'); // 1=FAQ, 2=link, 3=misc
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
        Schema::dropIfExists('differents');
    }
};
