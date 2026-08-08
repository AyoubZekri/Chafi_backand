<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\AppointmentDate;
use App\Models\Mypath;
use App\Function\Notification as NotificationService;
use Carbon\Carbon;

class SendAppointmentNotifications extends Command
{
    protected $signature = 'appointments:notify';
    protected $description = 'Send notifications for appointments using appointment_dates table';

    public function handle()
    {
        $todayMD = Carbon::now()->format('m-d');
        $service = new NotificationService();

        // Fetch all appointment dates along with their related appointment
        $appointmentDates = AppointmentDate::with('appointment')->get();

        foreach ($appointmentDates as $dateRecord) {
            $appointment = $dateRecord->appointment;
            
            // Skip if no related appointment
            if (!$appointment) {
                continue;
            }

            $alertDate = $dateRecord->alert_date;

            // If alert_date is empty, calculate it by subtracting 15 days from appointment_date
            if (empty($alertDate) && !empty($dateRecord->appointment_date)) {
                try {
                    // Use a leap year like 2024 to safely parse '02-29' if it exists
                    $parsedDate = Carbon::createFromFormat('Y-m-d', '2024-' . $dateRecord->appointment_date);
                    $alertDate = $parsedDate->subDays(15)->format('m-d');
                } catch (\Exception $e) {
                    continue; // Skip if date format is invalid
                }
            }

            // If the alert date equals today's date (m-d)
            if ($alertDate === $todayMD) {
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
        }

        $this->info('Appointment notifications sent for today.');
    }
}
