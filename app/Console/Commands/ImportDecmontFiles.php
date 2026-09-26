<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportDecmontFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:decmont {--dir=Decmont : The directory containing JSON files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Decmont JSON files into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dir = base_path($this->option('dir'));

        if (!is_dir($dir)) {
            $this->error("Directory {$dir} does not exist.");
            return;
        }

        $files = File::glob($dir . '/*.json');

        if (empty($files)) {
            $this->warn("No JSON files found in {$dir}.");
            return;
        }

        foreach ($files as $file) {
            $this->info("Processing " . basename($file));
            $this->importFile($file);
        }

        $this->info("Import completed successfully.");
    }

    protected function importFile($file)
    {
        $data = json_decode(file_get_contents($file), true);

        if (!$data || !isset($data['document'])) {
            $this->error("Invalid JSON format in " . basename($file));
            return;
        }

        DB::beginTransaction();

        try {
            // 1. Insert Document
            $docData = $data['document'];
            $documentId = DB::table('documents')->insertGetId([
                'code' => $docData['code'] ?? null,
                'title_ar' => $docData['title_ar'] ?? null,
                'title_fr' => $docData['title_fr'] ?? null,
                'year' => $docData['year'] ?? null,
                'source_file' => $docData['source_file'] ?? null,
                'pages' => $docData['pages'] ?? null,
                'legal_basis' => $docData['legal_basis'] ?? null,
                'preamble' => $docData['preamble'] ?? null,
                'is_active' => 1,
            ]);

            // Cache node types
            $nodeTypesCache = DB::table('node_types')->pluck('id', 'name_ar')->toArray();

            // 2. Insert Nodes
            $nodeMap = []; // old_id => new_id
            if (isset($data['nodes'])) {
                foreach ($data['nodes'] as $node) {
                    $typeName = $node['type'] ?? 'غير محدد';
                    if (!isset($nodeTypesCache[$typeName])) {
                        $newTypeId = DB::table('node_types')->insertGetId([
                            'name_ar' => $typeName,
                            'level_rank' => $node['rank'] ?? 10
                        ]);
                        $nodeTypesCache[$typeName] = $newTypeId;
                    }
                    $nodeTypeId = $nodeTypesCache[$typeName];

                    $parentId = null;
                    if (!empty($node['parent_id']) && isset($nodeMap[$node['parent_id']])) {
                        $parentId = $nodeMap[$node['parent_id']];
                    }

                    $newNodeId = DB::table('nodes')->insertGetId([
                        'document_id' => $documentId,
                        'parent_id' => $parentId,
                        'node_type_id' => $nodeTypeId,
                        'part' => $node['part'] ?? 'code',
                        'label' => $node['label'] ?? '',
                        'title' => $node['title'] ?? null,
                        'sort_order' => $node['sort_order'] ?? 0,
                        'page' => $node['page'] ?? null,
                    ]);

                    $nodeMap[$node['id']] = $newNodeId;

                    // Insert notes for node
                    if (!empty($node['notes']) && is_array($node['notes'])) {
                        foreach ($node['notes'] as $idx => $noteText) {
                            DB::table('notes')->insert([
                                'node_id' => $newNodeId,
                                'note_type' => 'other',
                                'text' => $noteText,
                                'sort_order' => $idx
                            ]);
                        }
                    }
                }
            }

            // 3. Insert Articles
            if (isset($data['articles'])) {
                foreach ($data['articles'] as $article) {
                    $nodeId = null;
                    if (!empty($article['node_id']) && isset($nodeMap[$article['node_id']])) {
                        $nodeId = $nodeMap[$article['node_id']];
                    }

                    $articleId = DB::table('articles')->insertGetId([
                        'document_id' => $documentId,
                        'node_id' => $nodeId,
                        'part' => $article['part'] ?? 'code',
                        'label' => $article['label'] ?? '',
                        'number' => $article['number'] ?? null,
                        'number_int' => $article['number_int'] ?? null,
                        'sort_order' => $article['sort_order'] ?? 0,
                        'text' => $article['text'] ?? '',
                        'is_repealed' => $article['is_repealed'] ?? 0,
                        'page_start' => $article['page_start'] ?? null,
                        'page_end' => $article['page_end'] ?? null,
                    ]);

                    // Insert notes for article
                    if (!empty($article['notes']) && is_array($article['notes'])) {
                        foreach ($article['notes'] as $idx => $noteText) {
                            DB::table('notes')->insert([
                                'article_id' => $articleId,
                                'note_type' => 'other',
                                'text' => $noteText,
                                'sort_order' => $idx
                            ]);
                        }
                    }

                    // Insert tables
                    if (!empty($article['tables']) && is_array($article['tables'])) {
                        foreach ($article['tables'] as $tableData) {
                            $tableId = DB::table('article_tables')->insertGetId([
                                'article_id' => $articleId,
                                'sort_order' => $tableData['sort_order'] ?? 0,
                                'title' => $tableData['title'] ?? null,
                                'n_rows' => $tableData['n_rows'] ?? 0,
                                'n_cols' => $tableData['n_cols'] ?? 0,
                                'page_start' => $tableData['page_start'] ?? null,
                                'page_end' => $tableData['page_end'] ?? null,
                            ]);

                            if (!empty($tableData['cells']) && is_array($tableData['cells'])) {
                                $cellsToInsert = [];
                                foreach ($tableData['cells'] as $cell) {
                                    $cellsToInsert[] = [
                                        'table_id' => $tableId,
                                        'row_idx' => $cell['row'] ?? 0,
                                        'col_idx' => $cell['col'] ?? 0,
                                        'row_span' => $cell['row_span'] ?? 1,
                                        'col_span' => $cell['col_span'] ?? 1,
                                        'is_header' => $cell['is_header'] ?? 0,
                                        'text' => $cell['text'] ?? '',
                                        'value_num' => null
                                    ];
                                }
                                
                                // Insert cells in chunks to avoid max placeholder limits
                                foreach (array_chunk($cellsToInsert, 100) as $chunk) {
                                    DB::table('article_table_cells')->insert($chunk);
                                }
                            }
                        }
                    }
                }
            }

            // Note: Currently skipping unplaced_notes and unplaced_tables as they lack article_id/node_id
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to import " . basename($file) . ": " . $e->getMessage());
        }
    }
}
