<?php

namespace App\Http\Controllers\Dashboard\institution;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function Editinstitution(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'type_institution' => 'sometimes|integer',
                // 'scope'            => 'sometimes|integer',
                "index" => 'nullable|integer',
                'id'               => 'required|integer|exists:institutions,id',
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

            $institution = Institution::find($data['id']);
            unset($data['id']);

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


            $institution->update($data);

            return Respons::success($institution,'تم التحديث بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التحديث',
                500,
                $e->getMessage()
            );
        }
    }

}
