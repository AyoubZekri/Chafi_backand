<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Node;

use App\Http\Controllers\Controller;
use App\Models\Node;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required', // door = أبواب, section = أقسام
            'document_id' => 'nullable|integer|exists:documents,id',
            'parent_id' => 'nullable|integer|exists:nodes,id',
            'search' => 'nullable|string|max:255',
            'as_tree' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $query = Node::query()->with(['document', 'nodeType']);
        
        // تحديد ما إذا كان المطلوب أبواب أو أقسام
        if ($request->type === 'door') {
            $query->whereHas('nodeType', function($q) {
                $q->where('name_ar', 'الباب');
            });
        } else if ($request->type === 'section') {
            $query->whereHas('nodeType', function($q) {
                $q->where('name_ar', 'القسم');
            });
            
            // فلترة الأقسام حسب الباب الذي تنتمي إليه
            if ($request->has('parent_id') && !empty($request->parent_id)) {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // جلب الأبواب/الأقسام حسب ملف معين إذا تم تمرير document_id
        if ($request->has('document_id') && !empty($request->document_id)) {
            $query->where('document_id', $request->document_id);
        }

        // البحث بالنص في العنوان
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
            });
        }

        // إذا تم طلب هيكل شجري
        if ($request->has('as_tree') && $request->as_tree) {
            $query->with('children'); 
            if ($request->type === 'door') {
                $query->whereNull('parent_id'); 
            }
        } else {
            $query->with('parent');
        }

        $nodes = $query->orderBy('sort_order')->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب الأبواب بنجاح',
            'data' => $nodes
        ]);
    }
}
