<?php

namespace App\Console\Commands;

use App\Enums\StatusEnum;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DeleteOldOrderGiftsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-order-gifts-command';

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
        $orders = Order::where('gift_end_date', '<', now())->where('status', StatusEnum::PENDING)->get();
        foreach ($orders as $order) {
            $order->update(['status' => StatusEnum::EXPIRED]);
        }
    }
}
