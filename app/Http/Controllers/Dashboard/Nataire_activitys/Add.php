<?php

namespace App\Http\Controllers\Dashboard\Nataire_activitys;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\NataireActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addNataireActivity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'            => 'nullable|string|max:255',
                'name_fr'         => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }
            $maxIndex = NataireActivity::max('index');
            $data = $validator->validated();
            $data['index'] = $maxIndex ? $maxIndex + 1 : 1;

            NataireActivity::create($data);

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
