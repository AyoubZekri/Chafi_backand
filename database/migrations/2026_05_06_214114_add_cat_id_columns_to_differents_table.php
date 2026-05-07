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
        Schema::table('differents', function (Blueprint $table) {
            $table->foreignId('cat_id')
                ->nullable()          // مهم جداً
                ->constrained('categories_differents')
                ->nullOnDelete()
                ->nullOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('differents', function (Blueprint $table) {
            $table->dropColumn('cat_id');

        });
    }
};
