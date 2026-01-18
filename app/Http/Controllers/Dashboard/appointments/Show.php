<?php

namespace App\Http\Controllers\Dashboard\appointments;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tax_id'            => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $query = Appointment::query();

            if ($request->filled('tax_id')) {
                $query->where('tax_id', $request->tax_id);
            }

            $data = $query->orderBy('index', 'asc')->get();
            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }

}
