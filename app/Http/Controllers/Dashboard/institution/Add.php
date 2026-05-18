<?php

namespace App\Http\Controllers\Dashboard\institution;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addinstitution(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type_institution' => 'nullable|integer',
                'scope'            => 'nullable|integer',
                "cat_id"=>"nullable|integer",
                'title'            => 'nullable|string|max:255',
                'body'             => 'nullable|string',
                'title_fr'         => 'nullable|string|max:255',
                'body_fr'          => 'nullable|string',
                "law_id"          => 'nullable|integer',
                'laws' => 'nullable|array',
                'laws.*.law_id' => 'nullable|integer',
                'laws.*.name_ar' => 'nullable|string',
                'laws.*.name_fr' => 'nullable|string',
                'laws.*.index_link' => 'nullable|integer',
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
            DB::beginTransaction();

            $maxIndex = Institution::where('type_institution', $request->type_institution)
                ->max('index');
            $data = $validator->validated();
            $laws = $data['laws'] ?? [];
            unset($data['laws']);
            $data['index'] = $maxIndex ? $maxIndex + 1 : 1;
            $institution = Institution::create($data);
                foreach ($laws as $law) {
                    $institution->laws()->create([
                        'law_id'        => $law['law_id'],
                        'name_ar'       => $law['name_ar'] ?? null,
                        'name_fr'       => $law['name_fr'] ?? null,
                        'index_link'    => $law['index_link'] ?? null,
                    ]);
                }
        DB::commit();

            return Respons::success('تم الإنشاء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return Respons::error(
                'حدث خطأ أثناء الإنشاء',
                500,
                $e->getMessage()
            );
        }
    }
}
