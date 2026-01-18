<?php

namespace App\Http\Controllers\Dashboard\activitys;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addActivitys(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'nataire_activitys_id' => 'nullable|integer',
                'tax_id' => 'nullable|integer',
                'status_tax' => 'nullable|integer',
                'code_activity' => 'nullable|integer',
                'name'            => 'nullable|string|max:255',
                'body'             => 'nullable|string',
                'name_fr'         => 'nullable|string|max:255',
                'body_fr'          => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            Activity::create($validator->validated());

            return Respons::success('تم الإنشاء بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء الإنشاء',
                500,
                $e->getMessage()
            );
        }
    }

}
