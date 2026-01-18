<?php

namespace App\Http\Controllers\Dashboard\Law;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Law;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Delete extends Controller
{
        public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:laws,id',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $law = Law::find($request->id);

            if ($law->pdf && Storage::disk('public')->exists($law->pdf)) {
                Storage::disk('public')->delete($law->pdf);
            }




            $law->delete();

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
