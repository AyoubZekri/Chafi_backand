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
            $table->integer('type'); // 1=FAQ, 2=link, 3=misc  4 = قاموس جبائي 
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('title_fr');
            $table->text('body_fr')->nullable();
            $table->foreignId('law_id')
                ->nullable()
                ->constrained('laws')
                ->nullOnDelete()
                ->nullOnUpdate();
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
