<?php

namespace App\Http\Controllers\AppLication;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Different;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Differents extends Controller
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

            $userId = auth()->id();

            $query = Different::query()
                ->where('type', $request->type)
                ->with('laws.law');

            if ($request->filled('cat_id')) {
                $query->where('cat_id', $request->cat_id);
            }

            if ($userId) {
                $query->with([
                    'reads' => function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    }
                ]);
            }

            $data = $query
                ->orderBy('index', 'asc')
                ->get()
                ->map(function ($item) use ($userId) {

                    return [
                        'id' => $item->id,
                        'cat_id' => $item->cat_id,

                        'title' => $item->title,
                        'title_fr' => $item->title_fr,

                        'body' => $item->body,
                        'body_fr' => $item->body_fr,

                        'calcul' => $item->calcul,
                        'index' => $item->index,

                        'is_read' => $userId
                            ? $item->reads->isNotEmpty()
                            : false,

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