<?php

namespace App\Http\Controllers\Dashboard\Mypaths;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Mypath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Delete extends Controller
{
    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:mypaths,id',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $Mypath = Mypath::find($request->id);

            $Mypath->delete();

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
