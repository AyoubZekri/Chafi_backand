<?php

namespace App\Http\Controllers\Dashboard\Notification;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
        public function show(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'tax_id' => 'nullable|integer',
                ]);

                if ($validator->fails()) {
                    return Respons::error(
                        'بيانات غير صحيحة',
                        422,
                        $validator->errors()
                    );
                }

                $query = Notification::query();

                if ($request->filled('tax_id')) {

                    if ((int)$request->tax_id === 4) {
                        $query->whereNull('tax_id');
                    } else {
                        $query->where('tax_id', $request->tax_id);
                    }

                }

                $data = $query->get();

                return Respons::success($data);

            } catch (\Exception $e) {
                return Respons::error('غير موجودة', 404,$e);
            }
        }


}
