<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class Edit extends Controller
{
    public function editArticle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:articles,id',
            'document_id' => 'nullable|exists:documents,id',
            'node_id' => 'nullable|exists:nodes,id',
            'part' => 'nullable|in:code,non_codified',
            'label' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:100',
            'number_int' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
            'text' => 'nullable|string',
            'label_en' => 'nullable|string|max:255',
            'text_en' => 'nullable|string',
            'is_repealed' => 'boolean',
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
            'notes' => 'nullable|array',
            'tables' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $article = Article::find($request->id);
            $article->update($request->all());

            if ($request->has('notes') && is_array($request->notes)) {
                $article->notes()->delete();
                foreach ($request->notes as $idx => $noteText) {
                    $article->notes()->create([
                        'note_type' => 'other',
                        'text' => $noteText,
                        'sort_order' => $idx
                    ]);
                }
            }

            if ($request->has('tables') && is_array($request->tables)) {
                $article->tables()->delete();
                foreach ($request->tables as $tableData) {
                    $table = $article->tables()->create([
                        'sort_order' => $tableData['sort_order'] ?? 0,
                        'title' => $tableData['title'] ?? null,
                        'n_rows' => $tableData['n_rows'] ?? 0,
                        'n_cols' => $tableData['n_cols'] ?? 0,
                        'page_start' => $tableData['page_start'] ?? null,
                        'page_end' => $tableData['page_end'] ?? null,
                    ]);

                    if (isset($tableData['cells']) && is_array($tableData['cells'])) {
                        foreach ($tableData['cells'] as $cell) {
                            $table->cells()->create([
                                'row_idx' => $cell['row_idx'] ?? ($cell['row'] ?? 0),
                                'col_idx' => $cell['col_idx'] ?? ($cell['col'] ?? 0),
                                'row_span' => $cell['row_span'] ?? 1,
                                'col_span' => $cell['col_span'] ?? 1,
                                'is_header' => $cell['is_header'] ?? 0,
                                'text' => $cell['text'] ?? '',
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'تم تعديل المادة (القسم) بنجاح',
                'data' => $article->load(['notes', 'tables.cells'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء التعديل: ' . $e->getMessage()
            ]);
        }
    }
}
