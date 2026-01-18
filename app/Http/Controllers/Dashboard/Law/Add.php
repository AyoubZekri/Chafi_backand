<?php

namespace App\Http\Controllers\Dashboard\Law;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Law;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
   public function addLaw(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'pdf' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
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

            if ($request->hasFile('pdf')) {
                $path = $request->file('pdf')->store('Law', 'public');
                $data['pdf'] = $path;
            }

            Law::create($data);

            return Respons::success('تم الإنشاء بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء الإنشاء',
                500,
                $e->getMessage()
            );
        }
    }

}
