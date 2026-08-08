<?php

namespace App\Http\Controllers\Dashboard\Notification;

use App\Function\Notification as FunctionNotification;
use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
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



            Notification::create($validator->validated());

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
                "id" => "required|integer|exists:notifications,id",
                'title'=> 'nullable|string|max:255',
                'body'=> 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $notification = \App\Models\Notification::find($request->id);
            $service = new \App\Function\Notification();
            $notification->Status = 1;
            $notification->save();
            $tax_id = $notification->tax_id;
            $userIds = [];

            if (!$tax_id) {
                $service->sendNotificationToTopic(
                    "user",
                    $request->title ?? $notification->title,
                    $request->body ?? $notification->content,
                );
                
                $userIds = \App\Models\User::pluck('id')->toArray();
            } else {
                $usersData = \App\Models\Mypath::where('tax_id', $tax_id)
                    ->join('users', 'mypaths.user_id', '=', 'users.id')
                    ->select('users.id', 'users.token')
                    ->get();

                $userIds = $usersData->pluck('id')->unique()->toArray();
                $userTokens = $usersData->pluck('token')->filter()->unique()->toArray();

                foreach ($userTokens as $token) {
                    $service->sendNotification(
                        $token,
                        $request->title ?? $notification->title,
                        $request->body ?? $notification->content
                    );
                }
            }

            if (!empty($userIds)) {
                $now = \Carbon\Carbon::now();
                $insertData = [];
                foreach ($userIds as $userId) {
                    $insertData[] = [
                        'user_id' => $userId,
                        'notification_id' => $notification->id,
                        'is_read' => 0,
                        'is_delete' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $chunks = array_chunk($insertData, 1000);
                foreach ($chunks as $chunk) {
                    \App\Models\NotificationUsers::insert($chunk);
                }
            }

            return Respons::success('تم الإرسال بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء الإرسال',
                500,
                $e->getMessage()
            );
        }
    }
    }
