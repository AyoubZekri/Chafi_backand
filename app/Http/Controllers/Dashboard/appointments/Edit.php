<?php

namespace App\Http\Controllers\Dashboard\appointments;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function Editappointments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "index" => 'nullable|integer',
                'id' => 'required|integer|exists:appointments,id',
                'tax_id' => 'nullable|integer',
                'declaration' => 'nullable|string|max:255',
                'dependencies' => 'nullable|string',
                'declaration_fr' => 'nullable|string|max:255',
                'dependencies_fr' => 'nullable|string',
                'deadline' => 'nullable|date_format:m-d',
                'noticeDate' => 'nullable|date_format:m-d',
                'appointment_dates' => 'nullable|array',
                'appointment_dates.*.appointment_date' => 'nullable|string',
                'appointment_dates.*.alert_date' => 'nullable|string',

            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $data = $validator->validated();

            $Different = Appointment::find($data['id']);
            if (!empty($data['deadline'])) {
                $data['deadline'] = '2000-' . $data['deadline'];
            }

            if (!empty($data['noticeDate'])) {
                $data['noticeDate'] = '2000-' . $data['noticeDate'];
            }
            if ($request->filled('index')) {

                $newIndex = $data['index'];
                $oldIndex = $Different->index;

                $other = Appointment::where('index', $newIndex)
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

            if ($request->has('appointment_dates')) {
                $Different->appointmentDates()->delete();
                foreach ($request->appointment_dates as $dateItem) {
                    if (!empty($dateItem['appointment_date']) || !empty($dateItem['alert_date'])) {
                        $Different->appointmentDates()->create([
                            'appointment_date' => $dateItem['appointment_date'] ?? null,
                            'alert_date' => $dateItem['alert_date'] ?? null,
                        ]);
                    }
                }
            }

            return Respons::success($Different, 'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }
}
