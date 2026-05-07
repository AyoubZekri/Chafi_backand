<?php

namespace App\Http\Controllers\Dashboard\Categories;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Categories_differents;
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
                'type' => 'nullable|integer',

            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }
            // 1 taxAndApps and category
            // 2 Category_diff and different
            $data = $validator->validated();
            if (!empty($data['type']) && $data['type'] == 2) {
                unset($data['type']);
                unset($data['tax_id']);
                $maxIndex = Categories_differents::max('index');
                $data['index'] = $maxIndex ? $maxIndex + 1 : 1;

                Categories_differents::create($data);
            } else {
                unset($data['type']);
                $maxIndex = Category::max('index');
                $data['index'] = $maxIndex ? $maxIndex + 1 : 1;
                Category::create($data);
            }
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
