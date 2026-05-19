<?php

namespace App\Http\Controllers\Dashboard\Mypaths;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Mypath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $query = Mypath::query()
                ->with([
                    'user:id,username,wilaya',
                    'activity:id,body,body_fr,code_activity,tax_id',
                    'nataire_activitys:id,name,name_fr'
                ])
                ->where('user_id', auth()->id());

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            $data = $query->get()->map(function ($item) {
                // بيانات mypaths الأصلية
                $mypathData = $item->toArray();

                // إضافة الأعمدة من العلاقات مباشرة
                $mypathData['username'] = $item->user?->username;
                $mypathData['wilaya'] = $item->user?->wilaya;
                $mypathData['activity_name'] = $item->activity?->body;
                $mypathData['activity_name_fr'] = $item->activity?->body_fr;
                $mypathData['code_activity'] = $item->activity?->code_activity;
                $mypathData['activity_tax_id'] = $item->activity?->tax_id;
                $mypathData['nataire_activitys_name'] = $item->nataire_activitys?->name;
                $mypathData['nataire_activitys_name_fr'] = $item->nataire_activitys?->name_fr;

                // حذف أي JSON nested
                unset($mypathData['user'], $mypathData['activity'], $mypathData['nataire_activitys']);

                return $mypathData;
            });

            return Respons::success($data);

        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }


}
