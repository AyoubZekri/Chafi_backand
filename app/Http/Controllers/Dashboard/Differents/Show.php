<?php

namespace App\Http\Controllers\Dashboard\Differents;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Different;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'type' => 'required|integer',
                'cat_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $query = Different::with('laws.law')
                ->where('type', $request->type);

            if ($request->type == 3 && !empty($request->cat_id)) {
                $query->where('cat_id', $request->cat_id);
            }

            $data = $query
                ->orderBy('index', 'asc')
                ->get()
                ->map(function ($item) {

                    return [
                        'id' => $item->id,
                        'cat_id' => $item->cat_id,

                        'title' => $item->title,
                        'title_fr' => $item->title_fr,

                        'body' => $item->body,
                        'body_fr' => $item->body_fr,

                        'calcul' => $item->calcul,
                        'index' => $item->index,
                        'type' => $item->type,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'laws' => $item->laws->map(function ($law) {
                            return [
                                'law_id' => $law->law_id,
                                'name_ar' => $law->name_ar,
                                'name_fr' => $law->name_fr,
                                'index_link' => $law->index_link,
                                'pdf' => optional($law->law)->pdf,
                            ];
                        }),
                    ];
                });

            return Respons::success($data);

        } catch (\Exception $e) {

            return Respons::error(
                'حدث خطأ',
                500,
                $e->getMessage()
            );
        }
    }
}