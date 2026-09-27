<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Document;

use App\Http\Controllers\Controller;
use App\Models\Document as DocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Delete extends Controller
{
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:documents,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $document = DocumentModel::find($request->id);
        if ($document->file && Storage::disk('public')->exists($document->file)) {
            Storage::disk('public')->delete($document->file);
        }
        $document->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الملف بنجاح'
        ]);
    }
}
