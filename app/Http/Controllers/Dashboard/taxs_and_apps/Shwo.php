<?php

namespace App\Http\Controllers\Dashboard\taxs_and_apps;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\TaxAndApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Shwo extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "tax_id"=>'nullable|integer',
                'cat_id' => 'nullable|integer',
                'type_cat' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $query = TaxAndApp::query();

            if ($request->filled('cat_id')) {
                $query->where('cat_id', $request->cat_id);
            }
            elseif ($request->filled('tax_id') && $request->filled('type_cat')) {
                $query->whereHas('category', function($q) use ($request) {
                    $q->where('tax_id', $request->tax_id)
                      ->where('type_cat', $request->type_cat);
                });
            }

            $data = $query->orderBy('index', 'asc')->get();

            return Respons::success($data);
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }
}
