<?php

namespace App\Http\Controllers\AppLication;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Institutions extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'scope'            => 'required|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $userId = auth()->id();

            $data = Institution::query()
                ->where('institutions.scope', $request->scope)
                ->orderBy('institutions.index', 'asc')
            ->with([
                'laws' => function ($q) {
                    $q->orderBy('index_link', 'asc')
                    ->with(['law:id,pdf']); // نجيب pdf من جدول laws
                },

                'reads' => function($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ])

                ->select(
                    'institutions.*',
                    'laws.pdf as pdf'
                )
                ->get()
                ->map(function($item) {
                $item->is_read = $item->reads->count() > 0;
                unset($item->reads);
                $item->laws = $item->laws->map(function ($law) {
                    return [
                        'law_id'     => $law->law_id,
                        'name_ar'    => $law->name_ar,
                        'name_fr'    => $law->name_fr,
                        'index_link' => $law->index_link,
                        'pdf'        => $law->law->pdf ?? null,
                    ];
                });

                return $item;
            });

            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404,$e);
        }
    }



}
