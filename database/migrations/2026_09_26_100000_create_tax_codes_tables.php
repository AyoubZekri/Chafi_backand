<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. documents (الوثائق / النصوص القانونية)
        // مخصص لتخزين المستندات أو النصوص القانونية الأساسية (مثل القوانين، المراسيم)
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 64);
            $table->string('title_ar', 255);
            $table->string('title_fr', 255)->nullable();
            $table->smallInteger('year')->unsigned()->nullable();
            $table->string('source_file', 255)->nullable();
            $table->smallInteger('pages')->unsigned()->nullable();
            $table->string('legal_basis', 500)->nullable();
            $table->mediumText('preamble')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['code', 'year'], 'uq_documents_code_year');
        });

        // 2. node_types (أنواع التقسيمات الهيكلية)
        // يحتوي على أنواع التقسيمات الهيكلية (الجزء، الكتاب، الباب، الفصل) وترتيبها الهرمي
        Schema::create('node_types', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name_ar', 50);
            $table->string('name_fr', 50)->nullable();
            $table->tinyInteger('level_rank')->unsigned();
            $table->unique('name_ar', 'uq_node_types_name');
        });

        // Insert node_types
        DB::table('node_types')->insert([
            ['id' => 1, 'name_ar' => 'الجزء',        'name_fr' => 'Partie',        'level_rank' => 1],
            ['id' => 2, 'name_ar' => 'الكتاب',       'name_fr' => 'Livre',         'level_rank' => 1],
            ['id' => 3, 'name_ar' => 'الباب',        'name_fr' => 'Titre',         'level_rank' => 2],
            ['id' => 4, 'name_ar' => 'الباب الفرعي', 'name_fr' => 'Sous-titre',    'level_rank' => 3],
            ['id' => 5, 'name_ar' => 'الفصل',        'name_fr' => 'Chapitre',      'level_rank' => 4],
            ['id' => 6, 'name_ar' => 'القسم',        'name_fr' => 'Section',       'level_rank' => 5],
            ['id' => 7, 'name_ar' => 'القسم الفرعي', 'name_fr' => 'Sous-section',  'level_rank' => 6],
            ['id' => 8, 'name_ar' => 'الفرع',        'name_fr' => 'Paragraphe',    'level_rank' => 7],
            ['id' => 9, 'name_ar' => 'قانون',        'name_fr' => 'Loi de finances', 'level_rank' => 1],
            ['id' => 10, 'name_ar' => 'موضوع',       'name_fr' => 'Objet',         'level_rank' => 2],
        ]);

        // 3. nodes (العُقَد / فهرس المحتويات)
        // يمثل الشجرة الهيكلية (الفهرس) للوثيقة ويربط بين الوثيقة والتقسيمات التي بداخلها
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->tinyInteger('node_type_id')->unsigned();
            $table->enum('part', ['code', 'non_codified'])->default('code');
            $table->string('label', 500);
            $table->string('title', 500)->nullable();
            $table->integer('sort_order')->unsigned()->default(0);
            $table->smallInteger('page')->unsigned()->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['document_id', 'parent_id', 'sort_order'], 'ix_nodes_tree');
            $table->foreign('document_id', 'fk_nodes_document')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('parent_id', 'fk_nodes_parent')->references('id')->on('nodes')->onDelete('cascade');
            $table->foreign('node_type_id', 'fk_nodes_type')->references('id')->on('node_types');
        });

        // 4. articles (المواد القانونية)
        // يخزن المحتوى الفعلي للنص القانوني (المواد) ويرتبط بالوثيقة والقسم الهيكلي
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned();
            $table->integer('node_id')->unsigned()->nullable();
            $table->enum('part', ['code', 'non_codified'])->default('code');
            $table->string('label', 255);
            $table->string('number', 100)->nullable();
            $table->integer('number_int')->unsigned()->nullable();
            $table->integer('sort_order')->unsigned()->default(0);
            $table->mediumText('text');
            $table->boolean('is_repealed')->default(0);
            $table->smallInteger('page_start')->unsigned()->nullable();
            $table->smallInteger('page_end')->unsigned()->nullable();
            $table->integer('version')->unsigned()->default(1);
            $table->string('updated_by', 100)->nullable();
            $table->string('change_reason', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['document_id', 'part', 'number_int'], 'ix_articles_number');
            $table->index(['document_id', 'sort_order'], 'ix_articles_order');
            $table->index('node_id', 'ix_articles_node');
            
            $table->foreign('document_id', 'fk_articles_document')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('node_id', 'fk_articles_node')->references('id')->on('nodes')->onDelete('set null');
        });
        
        // 5. notes (الملاحظات والهوامش)
        // لتخزين التهميشات أو الملاحظات المرتبطة بمادة معينة أو قسم معين
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->unsigned()->nullable();
            $table->integer('node_id')->unsigned()->nullable();
            $table->enum('note_type', ['amendment','creation','repeal','other'])->default('amendment');
            $table->text('text');
            $table->integer('sort_order')->unsigned()->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('article_id', 'ix_notes_article');
            $table->index('node_id', 'ix_notes_node');
            $table->foreign('article_id', 'fk_notes_article')->references('id')->on('articles')->onDelete('cascade');
            $table->foreign('node_id', 'fk_notes_node')->references('id')->on('nodes')->onDelete('cascade');
        });
        DB::statement('ALTER TABLE notes ADD CONSTRAINT ck_notes_owner CHECK (article_id IS NOT NULL OR node_id IS NOT NULL)');

        // 7. article_tables (الجداول المرفقة بالمواد)
        // يخزن البيانات الأساسية وهيكل الجداول التي قد تكون موجودة داخل المواد القانونية
        Schema::create('article_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->unsigned();
            $table->integer('sort_order')->unsigned();
            $table->string('title', 500)->nullable();
            $table->integer('n_rows')->unsigned()->default(0);
            $table->integer('n_cols')->unsigned()->default(0);
            $table->smallInteger('page_start')->unsigned()->nullable();
            $table->smallInteger('page_end')->unsigned()->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['article_id', 'sort_order'], 'uq_article_tables_order');
            $table->foreign('article_id', 'fk_article_tables_article')->references('id')->on('articles')->onDelete('cascade');
        });

        // 8. article_table_cells (خلايا الجداول)
        // يخزن البيانات الموجودة داخل كل خلية في الجداول
        Schema::create('article_table_cells', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('table_id')->unsigned();
            $table->integer('row_idx')->unsigned();
            $table->integer('col_idx')->unsigned();
            $table->smallInteger('row_span')->unsigned()->default(1);
            $table->smallInteger('col_span')->unsigned()->default(1);
            $table->boolean('is_header')->default(0);
            $table->text('text');
            $table->decimal('value_num', 20, 4)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['table_id', 'row_idx', 'col_idx'], 'uq_cells_position');
            $table->foreign('table_id', 'fk_cells_table')->references('id')->on('article_tables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('article_table_cells');
        Schema::dropIfExists('article_tables');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('nodes');
        Schema::dropIfExists('node_types');
        Schema::dropIfExists('documents');
    }
};
