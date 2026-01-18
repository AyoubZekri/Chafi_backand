<?php

namespace App\Http\Controllers\Dashboard\Mypaths;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Mypath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function EditMypath(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'     => 'required|integer|exists:mypaths,id',
                'tax_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $data = $validator->validated();

            $mypath = Mypath::where('id', $data['id'])
                ->where('user_id', auth()->id())      
                ->firstOrFail();

            unset($data['id']);

            $mypath->update($data);

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
