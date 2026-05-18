<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories_cat_insts', function (Blueprint $table) {
            $table->id();
            $table->integer('index');
            $table->string('name');
            $table->string('name_fr');
            $table->foreignId('cat_id')
                ->nullable()
                ->constrained('categories_institutions')
                ->nullOnDelete()
                ->nullOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_cat_inst');
    }
};
