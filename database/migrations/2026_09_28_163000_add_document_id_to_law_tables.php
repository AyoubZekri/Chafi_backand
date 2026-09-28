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
        Schema::table('law_taxs_and_app', function (Blueprint $table) {
            $table->integer('document_id')->unsigned()->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });

        Schema::table('institution_law', function (Blueprint $table) {
            $table->integer('document_id')->unsigned()->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });

        Schema::table('different_law', function (Blueprint $table) {
            $table->integer('document_id')->unsigned()->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('law_taxs_and_app', function (Blueprint $table) {
            $table->dropForeign(['document_id']);
            $table->dropColumn('document_id');
        });

        Schema::table('institution_law', function (Blueprint $table) {
            $table->dropForeign(['document_id']);
            $table->dropColumn('document_id');
        });

        Schema::table('different_law', function (Blueprint $table) {
            $table->dropForeign(['document_id']);
            $table->dropColumn('document_id');
        });
    }
};
