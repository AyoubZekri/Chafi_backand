<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\NotificationUsers;
use App\Models\Mypath;
use App\Models\User;
use App\Function\Notification as NotificationService;
use Carbon\Carbon;

class SendDailyNotifications extends Command
{
    protected $signature = 'notifications:daily';
    protected $description = 'Send notifications if timer equals today';

    public function handle()
    {
        $today = Carbon::today()->toDateString(); // yyyy-mm-dd

        $notifications = Notification::whereDate('timer', $today)
            ->where('Status', 0) // فقط غير المرسلة
            ->get();

        $service = new NotificationService();

        foreach ($notifications as $notification) {
            $notification->Status = 1;
            $notification->save();

            $tax_id = $notification->tax_id;
            $userIds = [];
            $userTokens = [];

            if (!$tax_id) {
                // إرسال للجميع
                $service->sendNotificationToTopic(
                    "user",
                    $notification->title,
                    $notification->content,
                );
                
                $userIds = User::pluck('id')->toArray();
            } else {
                // إرسال للمستخدمين المرتبطين بالـ tax_id
                $usersData = Mypath::where('tax_id', $tax_id)
                    ->join('users', 'mypaths.user_id', '=', 'users.id')
                    ->select('users.id', 'users.token')
                    ->get();
                
                $userIds = $usersData->pluck('id')->unique()->toArray();
                $userTokens = $usersData->pluck('token')->filter()->unique()->toArray();

                foreach ($userTokens as $token) {
                    $service->sendNotification(
                        $token,
                        $notification->title,
                        $notification->content
                    );
                }
            }

            // حفظ الإشعار للمستخدمين (أسرع طريقة عبر Bulk Insert)
            if (!empty($userIds)) {
                $now = Carbon::now();
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

                // تقسيم الإدخال إذا كان العدد كبيراً جداً (مثلاً أكثر من 1000)
                $chunks = array_chunk($insertData, 1000);
                foreach ($chunks as $chunk) {
                    NotificationUsers::insert($chunk);
                }
            }
        }

        $this->info('Daily notifications sent and saved for users.');
    }
}
