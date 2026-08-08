<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\AppointmentDate;
use Carbon\Carbon;

class MigrateAppointmentDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-appointment-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move deadline and noticeDate from appointments to appointment_dates table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appointments = Appointment::all();
        $count = 0;

        foreach ($appointments as $appointment) {
            $appointment_date = null;
            $alert_date = null;

            // Extract month and day (e.g. from 2000-10-25 -> 10-25)
            if (!empty($appointment->deadline)) {
                try {
                    $appointment_date = Carbon::parse($appointment->deadline)->format('m-d');
                } catch (\Exception $e) {
                    $appointment_date = substr($appointment->deadline, 5); // Fallback
                }
            }

            if (!empty($appointment->noticeDate)) {
                try {
                    $alert_date = Carbon::parse($appointment->noticeDate)->format('m-d');
                } catch (\Exception $e) {
                    $alert_date = substr($appointment->noticeDate, 5); // Fallback
                }
            }

            // Only create if there's at least one date
            if ($appointment_date || $alert_date) {
                // Check if it already exists to prevent duplicates
                $exists = AppointmentDate::where('appointment_id', $appointment->id)
                    ->where('appointment_date', $appointment_date)
                    ->where('alert_date', $alert_date)
                    ->exists();

                if (!$exists) {
                    AppointmentDate::create([
                        'appointment_id' => $appointment->id,
                        'appointment_date' => $appointment_date,
                        'alert_date' => $alert_date,
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Successfully migrated $count appointment dates.");
    }
}
