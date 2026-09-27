<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Delete extends Controller
{
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:articles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $article = Article::find($request->id);
        
        // مسح الملاحظات والجداول المرتبطة
        $article->notes()->delete();
        $article->tables()->delete(); // خلايا الجداول تُحذف تلقائياً إذا كانت قاعدة البيانات تحتوي على Cascade Delete

        $article->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المادة بنجاح'
        ]);
    }
}
