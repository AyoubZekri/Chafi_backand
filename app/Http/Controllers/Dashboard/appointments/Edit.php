<?php

namespace App\Http\Controllers\Dashboard\appointments;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function Editappointments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'id'               => 'required|integer|exists:appointments,id',
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

            $data = $validator->validated();

            $Different = Appointment::find($data['id']);
            unset($data['id']);

            $Different->update($data);

            return Respons::success($Different,'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }
}
