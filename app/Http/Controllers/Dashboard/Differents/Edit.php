<?php

namespace App\Http\Controllers\Dashboard\Differents;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Different;
use App\Models\DifferentLaw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
        public function EditDifferents(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'id'               => 'required|integer|exists:differents,id',
                'title'            => 'nullable|string|max:255',
                'body'             => 'nullable|string',
                'title_fr'         => 'nullable|string|max:255',
                'body_fr'          => 'nullable|string',
                'laws' => 'nullable|array',
                'laws.*.law_id' => 'required|integer',
                'laws.*.name_ar' => 'nullable|string',
                'laws.*.name_fr' => 'nullable|string',
                'laws.*.index_link' => 'nullable|integer',
                'law_id'           => 'nullable|integer',
                'index_link'       => 'nullable|string',
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
            $data = $validator->validated();
            $laws = $data['laws'] ?? [];
            unset($data['laws']);

            $Different = Different::find($data['id']);
            unset($data['id']);

             // swap index logic (كما هو)
             if ($request->filled('index')) {
                $newIndex = $data['index'];
                $oldIndex = $Different->index;

                $other = Different::where('index', $newIndex)
                    ->where('type', $Different->type)
                    ->where('id', '!=', $Different->id)
                    ->first();

                if ($other) {
                    $other->update([
                        'index' => $oldIndex
                    ]);
                }
            }
            $Different->update($data);

        if (!empty($laws)) {

            // حذف القديم
            $Different->laws()->delete();

            // إدخال الجديد (bulk insert أفضل)
            $insertData = [];

            foreach ($laws as $law) {
                $insertData[] = [
                    'different_id' => $Different->id,
                    'law_id'         => $law['law_id'],
                    'name_ar'        => $law['name_ar'] ?? null,
                    'name_fr'        => $law['name_fr'] ?? null,
                    'index_link'     => $law['index_link'] ?? null,
                ];
            }

            DifferentLaw::insert($insertData);
        }

        DB::commit();




            return Respons::success($Different,'تم التحديث بنجاح');
        } catch (\Exception $e) {

            return Respons::error(
                'حدث خطأ أثناء التحديث',
                500,
                $e->getMessage()
            );
        }
    }
}
