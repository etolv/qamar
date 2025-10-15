<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SettingService;
use Carbon\Carbon;
use Database\Seeders\SettingSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class AlertEmployeeExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:alert-employee-expire-command';

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
        $expiration_days = resolve(SettingService::class)->valueFromKey('employee_expiration_reminder', 30);
        $expired_residents = Employee::where('residence_expiration', '<=', Carbon::now()->subDays($expiration_days)->format('Y-m-d'))->get();
        foreach ($expired_residents as $expired_resident) {
            $this->info("Employee res {$expired_resident->user->name}");
            $notification_data = [
                'name' => _t("Employee Residence Expired"),
                'body' => _t("The employee {$expired_resident->user->name} residence expired on ") . Carbon::parse($expired_resident->residence_expiration)->format('Y-m-d'),
                'type' => 'admins',
            ];
            resolve(NotificationService::class)->store($notification_data);
        }
        $expired_insurances = Employee::where('insurance_expiration', '<=', Carbon::now()->subDays($expiration_days)->format('Y-m-d'))->get();
        foreach ($expired_insurances as $expired_insurance) {
            $this->info("Employee ins {$expired_insurance->user->name}");
            $notification_data = [
                'name' => _t("Employee Insurance Expired"),
                'body' => _t("The employee {$expired_insurance->user->name} insurance expired on ") . Carbon::parse($expired_insurance->residence_expiration)->format('Y-m-d'),
                'type' => 'admins',
            ];
            resolve(NotificationService::class)->store($notification_data);
        }
        $expired_cards = Employee::where('insurance_card_expiration', '<=', Carbon::now()->subDays($expiration_days)->format('Y-m-d'))->get();
        foreach ($expired_cards as $expired_card) {
            $this->info("Employee ins card {$expired_card->user->name}");
            $notification_data = [
                'name' => _t("Employee Insurance Card Expired"),
                'body' => _t("The employee {$expired_card->user->name} insurance card expired on ") . Carbon::parse($expired_card->residence_expiration)->format('Y-m-d'),
                'type' => 'admins',
            ];
            resolve(NotificationService::class)->store($notification_data);
        }
    }
}
