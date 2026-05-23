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

class Edit extends Controller
{
    public function editCategories(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                "index" => 'nullable|integer',
                'tax_id' => 'nullable|integer',
                'name' => 'nullable|string|max:255',
                'name_fr' => 'nullable|string|max:255',
                'type' => 'nullable|integer',
                "cat_id" => "nullable|integer",

            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            if (!empty($request->type) && $request->type == 2) {
                unset($request->type);
                unset($request->tax_id);
                unset($request->cat_id);
                $category = Categories_differents::find($request->id);
                $category->update($validator->validated());
            } else if (!empty($request->type) && $request->type == 3) {
                unset($request->type);
                unset($request->tax_id);
                unset($request->cat_id);
                $category = Categories_institutions::find($request->id);
                $category->update($validator->validated());
            } else if (!empty($request->type) && $request->type == 4) {
                unset($request->type);
                unset($request->tax_id);
                $category = Categories_cat_insts::find($request->id);
                $category->update($validator->validated());
            } else {
                unset($request->type);
                unset($request->cat_id);
                $category = Category::find($request->id);
                $category->update($validator->validated());
            }

            return Respons::success($category, 'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }

}
