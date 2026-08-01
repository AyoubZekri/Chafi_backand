<?php

namespace App\Http\Controllers\Dashboard\Bonusesandcompensation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Function\Respons;
use App\Models\Bonusesandcompensations;

class Edit extends Controller
{
    public function Edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'=> 'required|integer|exists:bonuses_and_compensations,id',
                'name_ar' => 'nullable|string|max:255',
                'name_fr' => 'nullable|string|max:255',
                'category' => 'nullable|integer',
                'is_required' => 'nullable|boolean',
                'type' => 'nullable|string',
                'value_type' => 'nullable|integer|in:1,2',
                'action_type' => 'nullable|integer|in:1,2',
                'has_special_logic' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $bonus = Bonusesandcompensations::find($request->id);

            if (!$bonus) {
                return Respons::error('العنصر غير موجود', 404);
            }

            $bonus->update($validator->validated());

            return Respons::success('تم التعديل بنجاح');

        } catch (\Exception $e) {

            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }
}
