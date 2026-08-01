<?php

namespace App\Http\Controllers\Dashboard\Bonusesandcompensation;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Bonusesandcompensations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
public function add(Request $request)
{
    try {

        $validator = Validator::make($request->all(), [
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

        Bonusesandcompensations::create($validator->validated());

        return Respons::success('تم الإنشاء بنجاح');

    } catch (\Exception $e) {

        return Respons::error(
            'حدث خطأ أثناء الإنشاء',
            500,
            $e->getMessage()
        );
    }
}}
