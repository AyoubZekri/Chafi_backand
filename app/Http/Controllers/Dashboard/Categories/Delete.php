<?php

namespace App\Http\Controllers\Dashboard\Categories;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Categories_cat_insts;
use App\Models\Categories_differents;
use App\Models\Categories_institutions;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Delete extends Controller
{
    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'type' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }
            $data = $validator->validated();
            if (!empty($data['type']) && $data['type'] == 2) {
                $cat = Categories_differents::find($request->id);
            } else if (!empty($data['type']) && $data['type'] == 3) {
                $cat = Categories_institutions::find($request->id);
            } else if (!empty($data['type']) && $data['type'] == 4) {
                $cat = Categories_cat_insts::find($request->id);
            } else {
                $cat = Category::find($request->id);
            }
            if (!$cat) {
                return Respons::error(
                    'البيانات غير موجودة',
                    422,
                    'البيانات غير موجودة'
                );
            }
            $cat->delete();

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
