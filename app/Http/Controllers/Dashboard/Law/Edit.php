<?php

namespace App\Http\Controllers\Dashboard\Law;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Law;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class Edit extends Controller
{
    public function editLaw(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'id'=> 'required|integer|exists:laws,id',
                'pdf' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'published_date' => 'nullable|date',
                'name' => 'nullable|string|max:255',
                'name_fr' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $data = $validator->validated();

            $law = Law::findOrFail($data['id']);

            if ($request->hasFile('pdf')) {
                if ($law->pdf && Storage::disk('public')->exists($law->pdf)) {
                    Storage::disk('public')->delete($law->pdf);
                }

                $path = $request->file('pdf')->store('Law', 'public');
                $data['pdf'] = $path;
            }

            $law->update($data);

            return Respons::success($law, 'تم التحديث بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التحديث',
                500,
                $e->getMessage()
            );
        }
    }

}
