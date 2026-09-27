<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id' => 'nullable|integer|exists:documents,id',
            'node_id' => 'nullable|integer|exists:nodes,id',
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        // جلب المواد مع العلاقات الهامة والجداول والملاحظات
        $query = Article::query()->with(['document', 'node', 'notes', 'tables.cells']);
        
        // جلب حسب الملف إذا تم توفيره
        if ($request->has('document_id') && !empty($request->document_id)) {
            $query->where('document_id', $request->document_id);
        }

        // جلب حسب الباب أو القسم (Node) إذا تم توفيره
        if ($request->has('node_id') && !empty($request->node_id)) {
            $query->where('node_id', $request->node_id);
        }

        // البحث بالنص في محتوى المادة أو عنوانها
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('text', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('sort_order')->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب المواد (الأقسام) بنجاح',
            'data' => $articles
        ]);
    }
}
