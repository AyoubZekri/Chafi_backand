<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Document;

use App\Http\Controllers\Controller;
use App\Models\Document as DocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:64',
            'title_ar' => 'required|string|max:255',
            'title_fr' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $data = $request->except('file');
        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('TaxSearch', 'public');
        }

        $document = DocumentModel::create($data);

        return response()->json([
            'status' => true,
            'message' => 'تمت إضافة الملف بنجاح',
            'data' => $document
        ]);
    }
}
