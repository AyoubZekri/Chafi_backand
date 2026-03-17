<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Mypath;
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

            if (!$tax_id) {
                // إرسال للجميع
                $service->sendNotificationToTopic(
                    "user",
                    $notification->title,
                    $notification->content,
                );
            } else {
                // إرسال للمستخدمين المرتبطين بالـ tax_id
                $userTokens = Mypath::where('tax_id', $tax_id)
                    ->join('users', 'mypaths.user_id', '=', 'users.id')
                    ->whereNotNull('users.token')
                    ->pluck('users.token')
                    ->unique()
                    ->toArray();

                foreach ($userTokens as $token) {
                    $service->sendNotification(
                        $token,
                        $notification->title,
                        $notification->content
                    );
                }
            }
        }

        $this->info('Daily notifications sent.');
    }
}
