<?php

namespace App\Console\Commands;

use App\Models\TempAttendance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteTempAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-temp-attendance-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        TempAttendance::where('created_at', '<=', Carbon::now()->subHour())->delete();
    }
}
