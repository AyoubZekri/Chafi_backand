<?php

namespace App\Http\Controllers\Dashboard\taxs_and_apps;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\TaxAndApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addtaxs_and_apps(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cat_id'            => 'nullable|integer',
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

            $maxIndex = TaxAndApp::max('index');
            $data = $validator->validated();
            $laws = $data['laws'] ?? [];
            unset($data['laws']);

            $data['index'] = $maxIndex ? $maxIndex + 1 : 1;

            $TaxAndApp = TaxAndApp::create($data);
                foreach ($laws as $law) {
                    $TaxAndApp->laws()->create([
                        'law_id'        => $law['law_id'],
                        'name_ar'       => $law['name_ar'] ?? null,
                        'name_fr'       => $law['name_fr'] ?? null,
                        'index_link'    => $law['index_link'] ?? null,
                    ]);
                }
        DB::commit();
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
