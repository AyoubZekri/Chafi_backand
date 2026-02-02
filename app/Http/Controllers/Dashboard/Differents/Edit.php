<?php

namespace App\Http\Controllers\Dashboard\Differents;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Different;
use Illuminate\Http\Request;
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

            $data = $validator->validated();

            $Different = Different::find($data['id']);
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
            unset($data['id']);

            $Different->update($data);

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
