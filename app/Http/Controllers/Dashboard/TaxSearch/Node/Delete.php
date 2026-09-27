<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Node;

use App\Http\Controllers\Controller;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Delete extends Controller
{
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:nodes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $node = Node::find($request->id);
        $node->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الباب بنجاح'
        ]);
    }
}
