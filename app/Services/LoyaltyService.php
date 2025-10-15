<?php

namespace App\Services;

use App\Enums\PaymentTypeEnum;
use App\Models\Loyalty;
use App\Models\Setting;
use Carbon\Carbon;
use Spatie\Permission\Commands\UpgradeForTeams;

/**
 * Class LoyaltyService.
 */
class LoyaltyService
{
    public function __construct(
        protected OrderService $orderService,
        protected SettingService $settingService,
        protected CustomerService $customerService
    ) {}

    public function store($item, $customer_id, $points)
    {
        $loyalty_points_period = $this->settingService->fromKey('loyalty_points_period')?->value ?? 1;
        $loyalty = Loyalty::create([
            'customer_id' => $customer_id,
            'model_type' => get_class($item),
            'model_id' => $item->id,
            'points' => $points,
            'expires_at' => Carbon::now()->addMonths($loyalty_points_period)->format('Y-m-d')
        ]);
        $loyalty->customer->increment('points', $loyalty->points);
        return $loyalty;
    }

    public function store_order_loyalty($order_id)
    {
        $order = $this->orderService->show($order_id);
        $total = $order->payments()->whereNotIn('type', [PaymentTypeEnum::POINT])->sum('amount');
        $cash_to_points = $this->settingService->fromKey('cash_to_points')?->value ?? 0;
        $points = $total * $cash_to_points;
        if ($points > 0)
            $loyalty = $this->store($order, $order->customer_id, $points);
        return true;
    }

    public function update_setting($settings)
    {
        foreach ($settings as $key => $setting) {
            Setting::where('key', $key)->update(['value' => $setting]);
        }
        return true;
    }

    public function all($customer_id = null, $paginate = false)
    {
        $query = Loyalty::when($customer_id, function ($query) use ($customer_id) {
            $query->where('customer_id', $customer_id);
        });
        if ($paginate)
            return $query->paginate();
        return $query->get();
    }

    public function decrementPoints($customer_id, $points)
    {
        $customer = $this->customerService->show($customer_id);
        $customer->decrement('points', ceil($points));
        $decremented = 0;
        foreach ($customer->loyalties as $loyalty) {
            if ($decremented < $points) {
                $toDecrement = min($loyalty->points, ceil($points - $decremented));
                $loyalty->decrement('points', $toDecrement);
                $decremented += $toDecrement;
                logger('to decrement' . $toDecrement);
                logger('points ' . $loyalty->points);
                if ($loyalty->points <= 0) {
                    $loyalty->delete();
                }
            } else {
                break;
            }
        }
        return $decremented;
    }
}
