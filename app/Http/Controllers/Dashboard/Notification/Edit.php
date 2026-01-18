<?php

namespace App\Http\Controllers\Dashboard\Notification;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function EditNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id"=>"required|integer|exists:notifications,id",
                "index" => 'nullable|integer',
                'timer' => 'nullable|date',
                'tax_id' => 'nullable|integer',
                'title' => 'nullable|string|max:255',
                'title_fr' => 'nullable|string|max:255',
                'content' => 'nullable|string|max:255',
                'content_fr' => 'nullable|string|max:255',
                'type_notification' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }


            $Notification = Notification::find($request->id);

            $Notification->update($validator->validated());

            return Respons::success($Notification,'تم التعديل بنجاح');

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
