<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('notifications:daily')->dailyAt("08:00");
Schedule::command('appointments:notify')->dailyAt('08:00');
Schedule::command('activities:translate')->dailyAt('02:00');
Schedule::command('notifications:monthly-updates')->monthlyOn(1, '08:00');
