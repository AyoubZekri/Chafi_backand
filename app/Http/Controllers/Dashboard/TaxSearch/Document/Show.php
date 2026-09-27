<?php

namespace App\Http\Controllers\Dashboard\TaxSearch\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $query = Document::query();
        
        // Search by code or title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_fr', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $documents = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب الملفات بنجاح',
            'data' => $documents
        ]);
    }
}
