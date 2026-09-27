<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Node;

use App\Http\Controllers\Controller;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function editNode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:nodes,id',
            'document_id' => 'nullable|exists:documents,id',
            'parent_id' => 'nullable|exists:nodes,id',
            'node_type_id' => 'nullable|exists:node_types,id',
            'part' => 'nullable|in:code,non_codified',
            'label' => 'nullable|string|max:500',
            'title' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'page' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $node = Node::find($request->id);
        $node->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'تم تعديل الباب بنجاح',
            'data' => $node
        ]);
    }
}
