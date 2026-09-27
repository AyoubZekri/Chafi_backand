<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Node;

use App\Http\Controllers\Controller;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addNode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|exists:documents,id',
            'parent_id' => 'nullable|exists:nodes,id',
            'node_type_id' => 'required|exists:node_types,id',
            'part' => 'nullable|in:code,non_codified',
            'label' => 'required|string|max:500',
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

        $node = Node::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'تمت إضافة الباب بنجاح',
            'data' => $node
        ]);
    }
}
