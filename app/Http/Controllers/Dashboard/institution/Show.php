<?php

namespace App\Http\Controllers\Dashboard\institution;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Categories_cat_insts;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'scope' => 'required|integer',
                // 'type_institution' => 'nullable|integer',
                'cat_id' => 'nullable|integer',
                'parints_cat' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $query = Institution::query();

            // if ($request->scope != 0) {
            //     $query->where('scope', $request->scope);
            // }

            // if ($request->filled('type_institution')) {
            //     $query->where('type_institution', $request->type_institution);
            // }

            if ($request->filled('cat_id')) {
                $query->where('cat_id', $request->cat_id);
            } elseif ($request->filled('parints_cat')) {
                $childCategoryIds = Categories_cat_insts::where('cat_id', $request->parints_cat)->pluck('id');
                $query->whereIn('cat_id', $childCategoryIds);
            }

            $data = $query->with(['laws.law', 'categories_cat_insts']) // تحميل الفئة الابن لجلب معرف الأب
                ->orderBy('index', 'asc')
                ->get()
                ->map(function ($item) {
                    $item->setRelation('laws', $item->laws->map(function ($law) {
                        return [
                            'law_id' => $law->law_id,
                            'name_ar' => $law->name_ar,
                            'name_fr' => $law->name_fr,
                            'index_link' => $law->index_link,
                            'pdf' => optional($law->law)->pdf,
                        ];
                    }));

                    // إضافة معرف الفئة الأب إلى الاستجابة
                    $item->parints_cat = optional($item->categories_cat_insts)->cat_id;
                    
                    // إزالة العلاقة من النتيجة النهائية لجعل الـ JSON أنظف (فقط الايدي يبقى)
                    $item->unsetRelation('categories_cat_insts');

                    return $item;
                });


            return Respons::success($data);
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }
}
