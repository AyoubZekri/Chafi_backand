<?php

namespace App\Http\Controllers\AppLication\NotificationUser;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\NotificationUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
public function show(Request $request)
{
    $validator = Validator::make($request->all(), [
        'tax_id' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
    }

    // tax_id الخاصة بالمستخدم من جدول mypaths
    $userTaxIds = \App\Models\Mypath::where('user_id', auth()->id())
        ->pluck('tax_id')
        ->unique()
        ->toArray();

$query = DB::table('notification_users')
    ->where('notification_users.user_id', auth()->id())
    ->where(function ($q) {
        $q->where('notification_users.is_delete', false)
          ->orWhereNull('notification_users.is_delete');
    })
    ->join('notifications', function ($join) {
        $join->on('notification_users.notification_id', '=', 'notifications.id')
             ->where('notifications.Status', 1);
    })
    ->select([
        'notification_users.id',
        'notifications.title',
        'notifications.content',
        'notifications.title_fr',
        'notifications.content_fr',
        'notifications.type_notification',
        'notifications.tax_id',
        'notifications.timer',
        'notification_users.is_read',
        'notification_users.created_at',
        'notification_users.updated_at',
    ]);
    // فلترة الإشعارات حسب مسارات المستخدم
    $query->where(function ($q) use ($userTaxIds) {
        $q->whereNull('notifications.tax_id') // إشعارات عامة
          ->orWhereIn('notifications.tax_id', $userTaxIds); // فقط اللي يطابق مسار المستخدم
    });

    // فلترة إضافية لو جا tax_id من الفرونت
    if ($request->filled('tax_id')) {
        if ((int)$request->tax_id === 4) {
            $query->whereNull('notifications.tax_id');
        } else {
            $query->where('notifications.tax_id', $request->tax_id);
        }
    }

    $data = $query->orderByDesc('notification_users.created_at')->get();

    return Respons::success($data);
}
    public static function IsRead(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            NotificationUsers::create([
                'notification_id' => $request->id,
                'is_read' => true,
                'user_id' => auth()->id(),
            ]);

            return Respons::success('تم التعديل بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }

}
