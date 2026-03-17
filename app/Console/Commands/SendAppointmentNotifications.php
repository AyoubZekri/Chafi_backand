<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Mypath;
use App\Function\Notification as NotificationService;
use Carbon\Carbon;

class SendAppointmentNotifications extends Command
{
    protected $signature = 'appointments:notify';
    protected $description = 'Send notifications for appointments whose noticeDate is today';

    public function handle()
    {
        $today = Carbon::today()->toDateString(); // yyyy-mm-dd

        $appointments = Appointment::whereDate('noticeDate', $today)->get();

        $service = new NotificationService();

        foreach ($appointments as $appointment) {
            $tax_id = $appointment->tax_id;

            $userTokens = Mypath::where('tax_id', $tax_id)
                ->join('users', 'mypaths.user_id', '=', 'users.id')
                ->whereNotNull('users.token')
                ->pluck('users.token')
                ->unique()
                ->toArray();

            foreach ($userTokens as $token) {
                $service->sendNotification(
                    $token,
                    "إقتراب موعد",
                    $appointment->declaration
                );
            }
        }

        $this->info('Appointment notifications sent for today.');
    }
}
