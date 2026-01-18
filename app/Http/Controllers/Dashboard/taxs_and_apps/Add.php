<?php

namespace App\Http\Controllers\Dashboard\taxs_and_apps;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\TaxAndApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addtaxs_and_apps(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'cat_id'            => 'nullable|integer',
                'title'            => 'nullable|string|max:255',
                'body'             => 'nullable|string',
                'title_fr'         => 'nullable|string|max:255',
                'body_fr'          => 'nullable|string',
                'law_id'           => 'nullable|integer',
                'index_link'       => 'nullable|integer',
                'calcul'           => 'nullable|string|max:255',
            ]);




            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $data=TaxAndApp::create($validator->validated());

            return Respons::success($data,'تم الإنشاء بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء الإنشاء',
                500,
                $e->getMessage()
            );
        }
    }

}
