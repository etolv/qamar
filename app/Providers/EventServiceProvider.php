<?php

namespace App\Providers;

use App\Events\StoreLoyaltyPoints;
use App\Listeners\StorePointsListener;
use App\Models\Attendance;
use App\Models\Bill;
use App\Models\BillType;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\CafeteriaOrder;
use App\Models\CashFlow;
use App\Models\Category;
use App\Models\City;
use App\Models\Coupon;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Package;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\State;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Vacation;
use App\Observers\AttendanceObserver;
use App\Observers\BillObserver;
use App\Observers\BillTypeObserver;
use App\Observers\BookingObserver;
use App\Observers\BranchObserver;
use App\Observers\BrandObserver;
use App\Observers\CafeteriaOrderObserver;
use App\Observers\CashFlowObserver;
use App\Observers\CategoryObserver;
use App\Observers\CityObserver;
use App\Observers\CouponObserver;
use App\Observers\EmployeeObserver;
use App\Observers\OrderObserver;
use App\Observers\PackageObserver;
use App\Observers\ProductObserver;
use App\Observers\RoleObserver;
use App\Observers\ServiceObserver;
use App\Observers\SettingObserver;
use App\Observers\ShiftObserver;
use App\Observers\StateObserver;
use App\Observers\StockObserver;
use App\Observers\SupplierObserver;
use App\Observers\VacationObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $observers = [
        Product::class => ProductObserver::class,
        Brand::class => BrandObserver::class,
        Category::class => CategoryObserver::class,
        State::class => StateObserver::class,
        City::class => CityObserver::class,
        CafeteriaOrder::class => CafeteriaOrderObserver::class,
        Supplier::class => SupplierObserver::class,
        Branch::class => BranchObserver::class,
        Service::class => ServiceObserver::class,
        Booking::class => BookingObserver::class,
        Role::class => RoleObserver::class,
        Stock::class => StockObserver::class,
        Coupon::class => CouponObserver::class,
        Setting::class => SettingObserver::class,
        Package::class => PackageObserver::class,
        Order::class => OrderObserver::class,
        Shift::class => ShiftObserver::class,
        Attendance::class => AttendanceObserver::class,
        CashFlow::class => CashFlowObserver::class,
        Vacation::class => VacationObserver::class,
        BillType::class => BillTypeObserver::class,
        Bill::class => BillObserver::class,
        Employee::class => EmployeeObserver::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
