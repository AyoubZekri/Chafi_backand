<?php

namespace App\Http\Controllers\Dashboard\Nataire_activitys;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\NataireActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function EditNataireActivity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'               => 'required|integer|exists:nataire_activitys,id',
                "index" => 'nullable|integer',
                'name'            => 'nullable|string|max:255',
                'name_fr'         => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $data = $validator->validated();

            $NataireActivity = NataireActivity::find($data['id']);
            unset($data['id']);

            $NataireActivity->update($data);

            return Respons::success($NataireActivity,'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }

}
