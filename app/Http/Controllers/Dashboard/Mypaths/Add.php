<?php

namespace App\Http\Controllers\Dashboard\Mypaths;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Mypath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addMypath(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "person_type" => 'nullable|integer',
                'nataire_activity_id' => 'nullable|integer',
                'activity_id' => 'nullable|integer',
                'tax_id' => 'nullable|integer',
                'activit_special' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }
            $data= $validator->validated();
            $data["user_id"] = auth()->id();

            Mypath::create($data);

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
