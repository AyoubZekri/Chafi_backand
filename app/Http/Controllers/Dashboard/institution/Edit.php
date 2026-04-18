<?php

namespace App\Http\Controllers\Dashboard\institution;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\InstitutionLaw;


class Edit extends Controller
{

public function Editinstitution(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:institutions,id',
            'index' => 'nullable|integer',

            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'title_fr' => 'nullable|string|max:255',
            'body_fr' => 'nullable|string',

            'laws' => 'nullable|array',
            'laws.*.law_id' => 'nullable|integer',
            'laws.*.name_ar' => 'nullable|string',
            'laws.*.name_fr' => 'nullable|string',
            'laws.*.index_link' => 'nullable|integer',
            'law_id'           => 'nullable|integer',
            'index_link'       => 'nullable|string',
            'calcul' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        DB::beginTransaction();

        $data = $validator->validated();

        $laws = $data['laws'] ?? [];
        unset($data['laws']);

        $institution = Institution::find($data['id']);
        unset($data['id']);

        // swap index logic (كما هو)
        if ($request->filled('index')) {
            $newIndex = $data['index'];
            $oldIndex = $institution->index;

            $otherInstitution = Institution::where('index', $newIndex)
                ->where('type_institution', $institution->type_institution)
                ->where('id', '!=', $institution->id)
                ->first();

            if ($otherInstitution) {
                $otherInstitution->update([
                    'index' => $oldIndex
                ]);
            }
        }

        // update institution
        $institution->update($data);

        // ❗ أهم نقطة: تحديث القوانين
        if (!empty($laws)) {

            // حذف القديم
            $institution->laws()->delete();

            // إدخال الجديد (bulk insert أفضل)
            $insertData = [];

            foreach ($laws as $law) {
                $insertData[] = [
                    'institution_id' => $institution->id,
                    'law_id'         => $law['law_id'],
                    'name_ar'        => $law['name_ar'] ?? null,
                    'name_fr'        => $law['name_fr'] ?? null,
                    'index_link'     => $law['index_link'] ?? null,
                ];
            }

            InstitutionLaw::insert($insertData);
        }

        DB::commit();

        return Respons::success($institution, 'تم التحديث بنجاح');

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
