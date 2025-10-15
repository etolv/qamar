<?php

namespace App\Providers;

use App\Enums\ProductTypeEnum;
use App\Enums\TaxTypeEnum;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Country;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('components.phone_with_code', function ($view) {
            $json = file_get_contents(public_path('assets/json/country_codes.json'));
            $countryCodes = json_decode($json, true);
            $view->with('countries_code', $countryCodes);
        });
        View::composer('dashboard.bill.add', function ($view) {
            $tax_types = TaxTypeEnum::cases();
            $view->with('tax_types', $tax_types);
        });
        View::composer('dashboard.*', function ($view) {
            // $countries_list = Country::get();
            // $settings = Setting::get();
            $tax_percentage = Cache::remember('tax_percentage', 60 * 60 * 24, fn() => (int)Setting::where('key', 'tax')->first()?->value ?? 15);
            // $view->with('website_settings', $settings);
            // $view->with('maxAllowedImages', $maxAllowedImages);

            $availableLocales = config('translation.locales');
            // $availableProductTypes = ProductTypeEnum::cases();
            // $view->with('availableProductTypes', $availableProductTypes);
            // $view->with('countries_list', $countries_list);
            $view->with('availableLocales', $availableLocales);
            //user
            $user = User::find(auth()->id());
            $notifications = Notification::whereHas('notificationUsers', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_read', false);
            })->get();
            $view->with('user_notifications', $notifications);
            $view->with('authenticated_user', $user);
            $branch = null;
            if ($user->user_type == 'employee') {
                $branch = $user->account->branch;
            }
            $view->with('user_branch', $branch);
            $view->with('tax_percentage', $tax_percentage);
            // if (auth()->user() && auth()->user()->account instanceof Company) {
            // $subscription = CompanySubscription::where('company_id', auth()->user()->account->id)->latest()->first();
            // $view->with('user_subscription', $subscription);
            // }
        });
    }
}
