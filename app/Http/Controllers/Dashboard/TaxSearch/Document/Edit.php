<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Document;

use App\Http\Controllers\Controller;
use App\Models\Document as DocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function editDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:documents,id',
            'code' => 'nullable|string|max:64',
            'title_ar' => 'nullable|string|max:255',
            'title_fr' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $document = DocumentModel::find($request->id);
        $document->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'تم تعديل الملف بنجاح',
            'data' => $document
        ]);
    }
}
