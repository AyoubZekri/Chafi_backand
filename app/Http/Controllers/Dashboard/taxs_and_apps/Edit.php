<?php

namespace App\Http\Controllers\Dashboard\taxs_and_apps;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\LawTaxAndApp;
use App\Models\TaxAndApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'cat_id'           => 'nullable|integer',
                'title'            => 'nullable|string|max:255',
                'body'             => 'nullable|string',
                'title_fr'         => 'nullable|string|max:255',
                'body_fr'          => 'nullable|string',
                'law_id'           => 'nullable|integer',
                'index_link'       => 'nullable|integer',
                'calcul'           => 'nullable|string|max:255',
                'laws' => 'nullable|array',
                'laws.*.law_id' => 'nullable|integer',
                'laws.*.name_ar' => 'nullable|string',
                'laws.*.name_fr' => 'nullable|string',
                'laws.*.index_link' => 'nullable|integer',

            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            DB::beginTransaction();
            $data = $validator->validated();

            $laws = $data['laws'] ?? [];
            unset($data['laws']);

            $TaxAndApp = TaxAndApp::find($data['id']);
            unset($data['id']);


            if ($request->filled('index')) {

                $newIndex = $data['index'];
                $oldIndex = $TaxAndApp->index;

                $type = $TaxAndApp->category->type_cat;

                // العنصر الآخر اللي عنده نفس index + نفس type
                $otherTaxAndApp = TaxAndApp::where('index', $newIndex)
                    ->where('id', '!=', $TaxAndApp->id)
                    ->whereHas('category', function ($q) use ($type) {
                        $q->where('type_cat', $type);
                    })
                    ->first();

                // swap
                if ($otherTaxAndApp) {
                    $otherTaxAndApp->update([
                        'index' => $oldIndex
                    ]);
                }
            }


            $TaxAndApp->update($data);

           if (!empty($laws)) {

            // حذف القديم
            $TaxAndApp->laws()->delete();

            // إدخال الجديد (bulk insert أفضل)
            $insertData = [];

            foreach ($laws as $law) {
                $insertData[] = [
                    'taxs_and_app_id' => $TaxAndApp->id,
                    'law_id'         => $law['law_id'],
                    'name_ar'        => $law['name_ar'] ?? null,
                    'name_fr'        => $law['name_fr'] ?? null,
                    'index_link'     => $law['index_link'] ?? null,
                ];
            }

            LawTaxAndApp::insert($insertData);
        }

        DB::commit();


        return Respons::success($TaxAndApp,'تم التحديث بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return Respons::error(
                'حدث خطأ أثناء التحديث',
                500,
                $e->getMessage()
            );
        }
    }
}
