<?php

namespace App\Http\Controllers\Dashboard\Categories;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addCategories(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tax_id' => 'nullable|integer',
                'type_cat' => 'nullable|integer',
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

            $maxIndex = Category::max('index');
            $data = $validator->validated();
            $data['index'] = $maxIndex ? $maxIndex + 1 : 1;

            Category::create($data);

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
