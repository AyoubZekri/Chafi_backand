<?php

namespace App\Http\Controllers\Dashboard\appointments;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addappointments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'tax_id' => 'nullable|integer',
                'declaration'            => 'nullable|string|max:255',
                'dependencies'             => 'nullable|string',
                'declaration_fr'         => 'nullable|string|max:255',
                'dependencies_fr'          => 'nullable|string',
                'deadline'           => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            Appointment::create($validator->validated());

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
