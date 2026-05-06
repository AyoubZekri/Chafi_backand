<?php

namespace App\Http\Controllers\Dashboard\appointments;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addappointments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tax_id' => 'nullable|integer',
                'declaration' => 'nullable|string|max:255',
                'dependencies' => 'nullable|string',
                'declaration_fr' => 'nullable|string|max:255',
                'dependencies_fr' => 'nullable|string',
                'deadline' => 'nullable|date_format:m-d',
                'noticeDate' => 'nullable|date_format:m-d',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }
            $maxIndex = Appointment::max('index');
            $data = $validator->validated();
            
            if (!empty($data['deadline'])) {
                $data['deadline'] = '2000-' . $data['deadline'];
            }

            if (!empty($data['noticeDate'])) {
                $data['noticeDate'] = '2000-' . $data['noticeDate'];
            }
            $data['index'] = $maxIndex ? $maxIndex + 1 : 1;

            Appointment::create($data);

            return Respons::success('تم الإنشاء بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء الإنشاء',
                500,
                $e->getMessage()
            );
        }
    }

    public function SendNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|integer|exists:appointments,id",
                'title' => 'nullable|string|max:255',
                'body' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $Appointment = Appointment::find($request->id);

            $tax_id = $Appointment->tax_id;

            $service = new \App\Function\Notification();

            $userTokens = \App\Models\Mypath::where('tax_id', $tax_id)
                ->join('users', 'mypaths.user_id', '=', 'users.id')
                ->whereNotNull('users.token')
                ->pluck('users.token')
                ->unique()
                ->toArray();

            foreach ($userTokens as $token) {
                $service->sendNotification(
                    $token,
                    $request->title,
                    $request->body
                );
            }

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
