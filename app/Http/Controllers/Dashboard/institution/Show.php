<?php

namespace App\Http\Controllers\Dashboard\institution;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'scope' => 'required|integer',
                // 'type_institution' => 'nullable|integer',
                'cat_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $query = Institution::query();

            // if ($request->scope != 0) {
            //     $query->where('scope', $request->scope);
            // }

            // if ($request->filled('type_institution')) {
            //     $query->where('type_institution', $request->type_institution);
            // }

            if ($request->filled('cat_id')) {
                $query->where('cat_id', $request->cat_id);
            }

            $data = $query->with('laws.law') // بدون تعقيد
                ->orderBy('index', 'asc')
                ->get()
                ->map(function ($item) {
                    $item->setRelation('laws', $item->laws->map(function ($law) {
                        return [
                            'law_id' => $law->law_id,
                            'name_ar' => $law->name_ar,
                            'name_fr' => $law->name_fr,
                            'index_link' => $law->index_link,
                            'pdf' => optional($law->law)->pdf,
                        ];
                    }));


                    return $item;
                });


            return Respons::success($data);
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }
}
