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
        Schema::table('stats', function (Blueprint $table) {
            // Drop old unique index
            $table->dropUnique(['device_id', 'open_date']);
            
            // Add new unique index including type_stats
            $table->unique(['device_id', 'open_date', 'type_stats']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stats', function (Blueprint $table) {
            $table->dropUnique(['device_id', 'open_date', 'type_stats']);
            $table->unique(['device_id', 'open_date']);
        });
    }
};
