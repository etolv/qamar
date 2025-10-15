<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\Customer;
use App\Models\OrderService;
use App\Models\OrderStock;
use App\Models\User;

/**
 * Class CustomerService.
 */
class CustomerService
{

    public function __construct(
        // protected WalletService $walletService,
        protected UserService $userService
    ) {}
    public function all($search = null, $paginated = false)
    {
        $query = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->with('account')->where('type_type', Customer::class)->latest();
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function service_and_product_history($customer_id)
    {
        $data = array();
        $data['services'] = OrderService::with(['order', 'service'])->whereHas('order', function ($query) use ($customer_id) {
            $query->where('customer_id', $customer_id)
                ->where('status', StatusEnum::COMPLETED->value);
        })->take(10)->get();
        $data['products'] = OrderStock::with(['order', 'stock.product'])->whereHas('order', function ($query) use ($customer_id) {
            $query->where('customer_id', $customer_id)
                ->where('status', StatusEnum::COMPLETED->value);
        })->take(10)->get();
        return $data;
    }

    public function points()
    {
        //
    }

    public function show($id)
    {
        return Customer::with(['user' => function ($query) {
            $query->withTrashed();
        }])->find($id);
    }

    public function store(User $user, array $data): Customer
    {
        $customer = new Customer;
        $customer->user()->associate($user);
        $customer->city_id = $data['city_id'];
        $customer->branch_id = $data['branch_id'];
        $customer->address = $data['address'];
        $customer->save();
        $user->account()->associate($customer);
        $user->save();
        resolve(AccountService::class)->store([
            'model_type' => Customer::class,
            'model_id' => $customer->id,
            'slug' => $user->name,
            'name' => $user->name,
            'is_debit' => false,
            'account_id' => resolve(AccountService::class)->fromSlug('client')?->id ?? null
        ]);
        // $this->walletService->store(['customer_id' => $customer->id]);
        return $customer;
    }

    public function update($data, $id)
    {
        $customer = Customer::find($id);
        $this->userService->update($data, $customer->user_id);
        return $customer;
    }

    public function destroy()
    {
        //
    }
}
