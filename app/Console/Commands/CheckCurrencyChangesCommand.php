<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckCurrencyChangesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-currency';

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
        $response = Http::get('https://sp-today.com/app_api/cur_damascus.json');
        if ($response->successful()) {
            $data = $response->json();
            $usd_ask = $data[0]['ask'];
            $usd_bid = $data[0]['bid'];
            $old_price = Cache::get('usd');
            if ($old_price != $usd_ask) {
                $message = "SP Today: USD Ask: $usd_ask, USD Bid: $usd_bid";
                Http::post("https://api.telegram.org/bot8127143215:AAHZO-wTfWqu6Ysq96YRoTBF3qYRhbVvik4/sendMessage", [
                    'chat_id' => '-4712426678',
                    'text' => $message,
                ]);
            }
            Cache::put('usd', $usd_ask, now()->addDays(1));
        }
    }
}
