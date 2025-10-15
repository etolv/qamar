<?php

namespace App\Console\Commands;

use App\Models\Loyalty;
use Illuminate\Console\Command;

class DeleteOldLoyaltyPointsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-loyalty-points-command';

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
        $loyalties = Loyalty::where('expires_at', '<', now())->get();
        foreach ($loyalties as $loyalty) {
            $loyalty->customer->decrement('points', $loyalty->points);
            $loyalty->delete();
        }
    }
}
