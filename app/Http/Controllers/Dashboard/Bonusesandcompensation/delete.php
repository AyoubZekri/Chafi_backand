<?php

namespace App\Http\Controllers\Dashboard\Bonusesandcompensation;


use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Bonusesandcompensations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class delete extends Controller
{
        public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:bonuses_and_compensations,id',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $cat = Bonusesandcompensations::find($request->id);

            $cat->delete();

            return Respons::success('تم الحذف بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء الحذف',
                500,
                $e->getMessage()
            );
        }
    }

}
