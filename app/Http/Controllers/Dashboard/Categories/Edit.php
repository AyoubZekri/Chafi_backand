<?php

namespace App\Http\Controllers\Dashboard\Categories;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
        public function editCategories(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'=> 'required|integer|exists:categories,id',
                "index" => 'nullable|integer',
                'tax_id' => 'nullable|integer',
                'name' => 'nullable|string|max:255',
                'name_fr' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }


            $category = Category::find($request->id);

            $category->update($validator->validated());

            return Respons::success($category,'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }

}
