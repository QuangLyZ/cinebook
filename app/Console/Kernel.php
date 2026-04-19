<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:publish-posts')->everyMinute();
        $schedule->command('notifications:send-showtime-reminders')->everyTenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}