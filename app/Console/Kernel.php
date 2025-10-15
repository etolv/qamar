<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:alert-employee-expire-command')->daily();
        $schedule->command('app:delete-old-order-gifts-command')->daily();
        $schedule->command('app:delete-old-loyalty-points-command')->daily();
        $schedule->command('app:add_missing_employee_attendance_command')->daily();
        $schedule->command('app:round-vacations-command')->cron('0 23 31 12 *'); // At 23:00 on day-of-month 31 in December
        // $schedule->command('app:round-vacations-command')->yearly();
        $schedule->command('app:delete-temp-attendance-command')->hourly();
        $schedule->command('disposable:update')->weekly();
        $schedule->command('app:check-currency')->everyMinute();
        // $schedule->command('app:check-currency')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
