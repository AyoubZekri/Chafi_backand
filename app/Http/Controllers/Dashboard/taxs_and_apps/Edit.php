<?php

namespace App\Http\Controllers\Dashboard\taxs_and_apps;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\TaxAndApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function EditTaxs_and_apps(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'type_institution' => 'sometimes|integer',
                // 'scope'            => 'sometimes|integer',
                "index" => 'nullable|integer',
                'id'               => 'required|integer|exists:taxs_and_apps,id',
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

            $data = $validator->validated();

            $TaxAndApp = TaxAndApp::find($data['id']);
            unset($data['id']);

            $TaxAndApp->update($data);

            return Respons::success($TaxAndApp,'تم التحديث بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التحديث',
                500,
                $e->getMessage()
            );
        }
    }
}
