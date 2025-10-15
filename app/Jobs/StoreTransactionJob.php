<?php

namespace App\Jobs;

use App\Enums\BillTypeEnum;
use App\Enums\CashFlowTypeEnum;
use App\Enums\ItemTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\StockWithdrawalTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Account;
use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderStock;
use App\Models\Transaction;
use App\Services\AccountService;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class StoreTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $accountService;
    public function __construct(protected $model)
    {
        $this->accountService = resolve(AccountService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $method = 'store_' . strtolower(class_basename($this->model));
        if (method_exists($this, $method)) {
            $this->$method($this->model);
        }
    }

    public function store_orderservicereturn($return)
    {
        if ($return->tax) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $return->tax,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.order_return_tax.from'))->id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.order_return_tax.to'))->id,
                'description' => "Order Return Service Tax #{$return->id}",
                'type' => TransactionTypeEnum::ORDER_RETURN_TAX->value
            ]);
            $transaction->model()->associate($return);
            $transaction->save();
        }
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $return->total,
            'from_account_id' => $this->accountService->fromSlug(config('account_map.order_return.from'))->id,
            'to_account_id' => $this->accountService->fromSlug(config('account_map.order_return.to'))->id,
            'description' => "Order Return Sale #{$return->id}",
            'type' => TransactionTypeEnum::ORDER_RETURN->value
        ]);
        $transaction->model()->associate($return);
        $transaction->save();
    }

    public function store_generatedsalary($generated_salary)
    {
        $generated_salary->transactions()->delete();
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $generated_salary->total,
            'from_account_id' => $this->accountService->fromSlug(config('account_map.salary.from'))->id,
            'to_account_id' => $this->accountService->fromSlug(config('account_map.salary.to'))->id,
            'description' => "Employee {$generated_salary->employee->user->name} #{$generated_salary->id}",
            'type' => TransactionTypeEnum::SALARY->value
        ]);
        $transaction->model()->associate($generated_salary);
        $transaction->save();
    }

    public function store_cashflow($cash_flow)
    {
        $employee_account = $this->accountService->fromSlug($cash_flow->flowable?->user?->name ?? 'employee')?->id ?? $this->accountService->fromSlug('employee')->id;
        if (in_array($cash_flow->type, [CashFlowTypeEnum::ADVANCE, CashFlowTypeEnum::GIFT])) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $cash_flow->amount,
                'from_account_id' => $employee_account,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.cash_flow.to'))->id,
                'description' => "Employee {$cash_flow->type->name} #{$cash_flow->id}",
                'type' => $cash_flow->type == CashFlowTypeEnum::ADVANCE ? TransactionTypeEnum::CASH_ADVANCE->value : TransactionTypeEnum::CASH_GIFT->value
            ]);
            $transaction->model()->associate($cash_flow);
            $transaction->save();
        } else if ($cash_flow->type == CashFlowTypeEnum::DEDUCT) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $cash_flow->amount,
                'from_account_id' => $this->accountService->fromSlug('salary_expense')->id,
                'to_account_id' => $employee_account,
                'description' => "Employee {$cash_flow->type->name} #{$cash_flow->id}",
                'type' => TransactionTypeEnum::CASH_DEDUCT->value
            ]);
            $transaction->model()->associate($cash_flow);
            $transaction->save();
        } else if ($cash_flow->type == CashFlowTypeEnum::EXPENSE) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $cash_flow->amount,
                'from_account_id' => $this->accountService->fromSlug('owner')->id,
                'to_account_id' => $this->accountService->fromSlug('cash')->id,
                'description' => "Owner Withdrawal #{$cash_flow->id}",
                'type' => TransactionTypeEnum::CASH_EXPENSE->value
            ]);
            $transaction->model()->associate($cash_flow);
            $transaction->save();
        }
    }

    public function store_stockwithdrawal($stock_withdrawal)
    {
        // if ($stock_withdrawal->tax) {
        //     $transaction = new Transaction([
        //         'is_automatic' => true,
        //         'amount' => $stock_withdrawal->tax,
        //         'from_account_id' => $this->accountService->fromSlug(config('account_map.stock_withdrawal_tax.from'))->id,
        //         'to_account_id' => $this->accountService->fromSlug(config('account_map.stock_withdrawal_tax.to'))->id,
        //         'description' => "Stock Withdrawal Tax Collected #{$stock_withdrawal->id}",
        //     ]);
        //     $transaction->model()->associate($stock_withdrawal);
        //     $transaction->save();
        // }
        if ($stock_withdrawal->type == StockWithdrawalTypeEnum::EXCHANGE) {
            $from_account_id = $this->accountService->fromSlug($stock_withdrawal->employee->user->name)?->id ?? $this->accountService->fromSlug('employee')->id;
        } else {
            $from_account_id = $this->accountService->fromSlug(config('account_map.withdrawal_account.' . $stock_withdrawal->type->name))->id;
        }
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $stock_withdrawal->quantity * $stock_withdrawal->price,
            'from_account_id' => $from_account_id,
            'to_account_id' => $this->accountService->fromSlug(config('account_map.stock_withdrawal.to'))->id,
            'description' => "Stock Withdrawal #{$stock_withdrawal->id}",
            'type' => TransactionTypeEnum::STOCK_WITHDRAWAL
        ]);
        $transaction->model()->associate($stock_withdrawal);
        $transaction->save();
    }

    public function store_billreturn($bill_return)
    {
        $supplier_account_id = $this->accountService->fromSlug($bill_return->supplier?->name ?? 'supplier')?->id ?? $this->accountService->fromSlug('supplier')->id;
        $bill_return_to_id = $this->accountService->fromSlug(config('account_map.bill_return.to'))->id;
        if ($bill_return->tax) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $bill_return->tax,
                'from_account_id' => $supplier_account_id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.bill_return_tax.to'))->id,
                'description' => "Bill Return Tax Paid #{$bill_return->id}",
                'type' => TransactionTypeEnum::BILL_RETURN_TAX->value
            ]);
            $transaction->model()->associate($bill_return);
            $transaction->save();
        }
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $bill_return->total,
            'from_account_id' => $supplier_account_id,
            'to_account_id' => $bill_return_to_id,
            'description' => "Bill Return #{$bill_return->id}",
            'type' => TransactionTypeEnum::BILL_RETURN->value
        ]);
        $transaction->model()->associate($bill_return);
        $transaction->save();
        // payments
        foreach ($bill_return->payments as $payment) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $payment->amount,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.payments_type.' . $payment->type->name))->id,
                'to_account_id' => $supplier_account_id,
                'description' => "Bill Return Payment #{$bill_return->id}",
                'type' => TransactionTypeEnum::BILL_RETURN_PAYMENT->value
            ]);
            $transaction->model()->associate($payment);
            $transaction->save();
        }
    }

    public function store_payment($payment)
    {
        $bill = $payment->model;
        $supplier_account_id = $this->accountService->fromSlug($bill->supplier?->name ?? 'supplier')?->id ?? $this->accountService->fromSlug('supplier')->id;
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $payment->amount,
            'from_account_id' => $supplier_account_id,
            'to_account_id' => $this->accountService->fromSlug(config('account_map.payments_type.' . $payment->type->name))->id,
            'description' => "Bill Payment #{$bill->id}",
            'type' => $bill->type == BillTypeEnum::EXPENSE ? TransactionTypeEnum::BILL_EXPENSE_PAYMENT->value :  TransactionTypeEnum::BILL_PURCHASE_PAYMENT->value
        ]);
        $transaction->model()->associate($payment);
        $transaction->save();
    }

    public function store_bill($bill)
    {
        DB::beginTransaction();
        $supplier_account_id = $this->accountService->fromSlug($bill->supplier?->name ?? 'supplier')?->id ?? $this->accountService->fromSlug('supplier')->id;
        if ($bill->type == BillTypeEnum::PURCHASE) {
            $description = "Purchase";
            $bill_from_id = $this->accountService->fromSlug(config('account_map.bill.from'))->id;
        } else {
            $description = "Expense";
            $bill_from_id = $this->accountService->fromSlug($bill->billType?->name ?? config('account_map.expense.from'))?->id ?? $this->accountService->fromSlug(config('account_map.expense.from'))?->id;
        }
        if ($bill->tax) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $bill->tax,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.bill_tax.from'))->id,
                'to_account_id' => $supplier_account_id,
                'description' => "$description Tax Collected #{$bill->id}",
                'type' => $bill->type == BillTypeEnum::EXPENSE ? TransactionTypeEnum::BILL_EXPENSE_TAX->value :  TransactionTypeEnum::BILL_PURCHASE_TAX->value
            ]);
            $transaction->model()->associate($bill);
            $transaction->save();
        }
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $bill->total,
            'from_account_id' => $bill_from_id,
            'to_account_id' => $supplier_account_id,
            'description' => "$description #{$bill->id}",
            'type' => $bill->type == BillTypeEnum::EXPENSE ? TransactionTypeEnum::BILL_EXPENSE->value :  TransactionTypeEnum::BILL_PURCHASE->value
        ]);
        $transaction->model()->associate($bill);
        $transaction->save();
        foreach ($bill->payments as $payment) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $payment->amount,
                'from_account_id' => $supplier_account_id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.payments_type.' . $payment->type->name))->id,
                'description' => "$description Payment #{$bill->id}",
                'type' => $bill->type == BillTypeEnum::EXPENSE ? TransactionTypeEnum::BILL_EXPENSE_PAYMENT->value :  TransactionTypeEnum::BILL_PURCHASE_PAYMENT->value
            ]);
            $transaction->model()->associate($payment);
            $transaction->save();
        }
        DB::commit();
    }

    public function store_cafeteriaorder($order)
    {
        DB::beginTransaction();
        if ($order->tax) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $order->tax,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.order_tax.from'))->id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.order_tax.to'))->id,
                'description' => "Cafeteria Order Tax Collected #{$order->id}",
                'type' => TransactionTypeEnum::CAFETERIA_ORDER_TAX->value
            ]);
            $transaction->model()->associate($order);
            $transaction->save();
        }
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $order->total,
            'from_account_id' => $this->accountService->fromSlug(config('account_map.order.from'))->id,
            'to_account_id' => $this->accountService->fromSlug(config('account_map.order.to'))->id,
            'description' => "Cafeteria Order Sale #{$order->id}",
            'type' => TransactionTypeEnum::CAFETERIA_ORDER_SALE->value
        ]);
        $transaction->model()->associate($order);
        $transaction->save();
        foreach ($order->payments as $payment) {
            if ($payment->type != PaymentTypeEnum::POINT && $payment->status == PaymentStatusEnum::PAID) {
                $transaction = new Transaction([
                    'is_automatic' => true,
                    'amount' => $payment->amount,
                    'from_account_id' => $this->accountService->fromSlug(config('account_map.payments_type.' . $payment->type->name))->id,
                    'to_account_id' => $this->accountService->fromSlug(config('account_map.order_payment.to'))->id,
                    'description' => "Cafeteria Order Payment #{$order->id}",
                    'type' => TransactionTypeEnum::CAFETERIA_ORDER_PAYMENT->value
                ]);
                $transaction->model()->associate($payment);
                $transaction->save();
            }
        }
        $order_expense = 0;
        $order->cafeteriaOrderStocks->each(function ($orderStock) use (&$order_expense, $order) {
            $bill_product = $orderStock->stock->billProduct;
            $product_cost = $bill_product->exchange_price - ($bill_product->tax / $bill_product->convert);
            $order_expense += $product_cost * $orderStock->quantity;
        });
        if ($order_expense) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $order_expense,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.order_expense.from'))->id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.order_expense.to'))->id,
                'description' => "Sales Expense Cafeteria Order #{$order->id}",
                'type' => TransactionTypeEnum::CAFETERIA_ORDER_WAREHOUSE_EXPENSE->value
            ]);
            $transaction->model()->associate($order);
            $transaction->save();
        }
        DB::commit();
    }

    public function store_order(Order $order)
    {
        DB::beginTransaction();
        if ($order->tax) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $order->tax,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.order_tax.from'))->id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.order_tax.to'))->id,
                'description' => "Order Tax Collected #{$order->id}",
                'type' => TransactionTypeEnum::ORDER_TAX->value
            ]);
            $transaction->model()->associate($order);
            $transaction->save();
        }
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $order->total,
            'from_account_id' => $this->accountService->fromSlug(config('account_map.order.from'))->id,
            'to_account_id' => $this->accountService->fromSlug(config('account_map.order.to'))->id,
            'description' => "Order Sale #{$order->id}",
            'type' => TransactionTypeEnum::ORDER_SALE->value
        ]);
        $transaction->model()->associate($order);
        $transaction->save();
        foreach ($order->payments as $payment) {
            if ($payment->type != PaymentTypeEnum::POINT && $payment->status == PaymentStatusEnum::PAID) {
                $transaction = new Transaction([
                    'is_automatic' => true,
                    'amount' => $payment->amount,
                    'from_account_id' => $this->accountService->fromSlug(config('account_map.payments_type.' . $payment->type->name))->id,
                    'to_account_id' => $this->accountService->fromSlug(config('account_map.order_payment.to'))->id,
                    'description' => "Order Payment #{$order->id}",
                    'type' => TransactionTypeEnum::ORDER_PAYMENT->value
                ]);
                $transaction->model()->associate($payment);
                $transaction->save();
            }
        }
        $order_expense = 0;
        $order->orderStocks->each(function ($orderStock) use (&$order_expense, $order) {
            $bill_product = $orderStock->stock->billProduct;
            $product_cost = $bill_product->exchange_price - ($bill_product->tax / $bill_product->convert);
            if ($orderStock->type == ItemTypeEnum::SERVICE) {
                $orderService = $orderStock->orderService;
                $employee_account = $orderService?->employee?->account?->id ?? $order?->employee?->account?->id ?? $this->accountService->fromSlug('employee')->id;
                $transaction = new Transaction([
                    'is_automatic' => true,
                    'amount' => $product_cost * $orderService->quantity,
                    'from_account_id' => $employee_account,
                    'to_account_id' => $this->accountService->fromSlug('warehouse')->id,
                    'description' => "Sales Employee Expense. Order #{$order->id}",
                    'type' => TransactionTypeEnum::ORDER_EMPLOYEE_EXPENSE->value
                ]);
                $transaction->model()->associate($order);
                $transaction->save();
            } else {
                $order_expense += $product_cost * $orderStock->quantity;
            }
        });
        if ($order_expense) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $order_expense,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.order_expense.from'))->id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.order_expense.to'))->id,
                'description' => "Sales Expense Order #{$order->id}",
                'type' => TransactionTypeEnum::ORDER_WAREHOUSE_EXPENSE->value
            ]);
            $transaction->model()->associate($order);
            $transaction->save();
        }
        // TODO check again since the profit is not deducted until the employee meets the threshold
        // $order->orderServices->each(function ($orderService) use (&$order_service_expense, $order) {
        //     $employee_account = $orderService?->employee?->account?->id ?? $order?->employee?->account?->id ?? $this->accountService->fromSlug('employee')->id;
        //     $profit_percentage = $orderService?->employee?->salary?->profit_percentage ?? resolve(SettingService::class)->valueFromKey('employee_profit_percentage', 12);
        //     $profit = ($orderService->price * $orderService->quantity) * ($profit_percentage / 100);
        //     $transaction = new Transaction([
        //         'is_automatic' => true,
        //         'amount' => $profit,
        //         'from_account_id' => $this->accountService->fromSlug('warehouse')->id,
        //         'to_account_id' => $employee_account,
        //         'description' => "Sales Employee Profit Expense. Order #{$order->id}",
        //     ]);
        //     $transaction->model()->associate($order);
        //     $transaction->save();
        // });
        DB::commit();
    }

    public function store_booking(Booking $booking)
    {
        DB::beginTransaction();
        if ($booking->tax) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $booking->tax,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.order_tax.from'))->id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.order_tax.to'))->id,
                'description' => "Booking Tax Collected #{$booking->id}",
                'type' => TransactionTypeEnum::BOOKING_TAX->value
            ]);
            $transaction->model()->associate($booking);
            $transaction->save();
        }
        $transaction = new Transaction([
            'is_automatic' => true,
            'amount' => $booking->total,
            'from_account_id' => $this->accountService->fromSlug(config('account_map.order.from'))->id,
            'to_account_id' => $this->accountService->fromSlug(config('account_map.order.to'))->id,
            'description' => "Booking Sale #{$booking->id}",
            'type' => TransactionTypeEnum::BOOKING_SALE->value
        ]);
        $transaction->model()->associate($booking);
        $transaction->save();
        foreach ($booking->payments as $payment) {
            if ($payment->type != PaymentTypeEnum::POINT && $payment->status == PaymentStatusEnum::PAID) {
                $transaction = new Transaction([
                    'is_automatic' => true,
                    'amount' => $payment->amount,
                    'from_account_id' => $this->accountService->fromSlug(config('account_map.payments_type.' . $payment->type->name))->id,
                    'to_account_id' => $this->accountService->fromSlug(config('account_map.order_payment.to'))->id,
                    'description' => "Booking Payment #{$booking->id}",
                    'type' => TransactionTypeEnum::BOOKING_PAYMENT->value
                ]);
                $transaction->model()->associate($payment);
                $transaction->save();
            }
        }
        $booking_expense = 0;
        $booking->bookingProducts->each(function ($bookingProduct) use (&$booking_expense, $booking) {
            $bill_product = $bookingProduct->stock->billProduct;
            $product_cost = $bill_product->exchange_price - ($bill_product->tax / $bill_product->convert);
            if ($bookingProduct->type == ItemTypeEnum::SERVICE) {
                $bookingService = $bookingProduct->bookingService;
                $employee_account = $orderService?->employee?->account?->id ?? $order?->employee?->account?->id ?? $this->accountService->fromSlug('employee')->id;
                $transaction = new Transaction([
                    'is_automatic' => true,
                    'amount' => $product_cost * $bookingService->quantity,
                    'from_account_id' => $employee_account,
                    'to_account_id' => $this->accountService->fromSlug('warehouse')->id,
                    'description' => "Sales Employee Expense. Booking #{$booking->id}",
                    'type' => TransactionTypeEnum::BOOKING_EMPLOYEE_EXPENSE->value
                ]);
                $transaction->model()->associate($booking);
                $transaction->save();
            } else {
                $booking_expense += $product_cost * $bookingProduct->quantity;
            }
        });
        if ($booking_expense) {
            $transaction = new Transaction([
                'is_automatic' => true,
                'amount' => $booking_expense,
                'from_account_id' => $this->accountService->fromSlug(config('account_map.order_expense.from'))->id,
                'to_account_id' => $this->accountService->fromSlug(config('account_map.order_expense.to'))->id,
                'description' => "Sales Expense Booking #{$booking->id}",
                'type' => TransactionTypeEnum::BOOKING_WAREHOUSE_EXPENSE->value
            ]);
            $transaction->model()->associate($booking);
            $transaction->save();
        }
        // TODO check again since the profit is not deducted until the employee meets the threshold
        // $order->orderServices->each(function ($orderService) use (&$order_service_expense, $order) {
        //     $employee_account = $orderService?->employee?->account?->id ?? $order?->employee?->account?->id ?? $this->accountService->fromSlug('employee')->id;
        //     $profit_percentage = $orderService?->employee?->salary?->profit_percentage ?? resolve(SettingService::class)->valueFromKey('employee_profit_percentage', 12);
        //     $profit = ($orderService->price * $orderService->quantity) * ($profit_percentage / 100);
        //     $transaction = new Transaction([
        //         'is_automatic' => true,
        //         'amount' => $profit,
        //         'from_account_id' => $this->accountService->fromSlug('warehouse')->id,
        //         'to_account_id' => $employee_account,
        //         'description' => "Sales Employee Profit Expense. Order #{$order->id}",
        //     ]);
        //     $transaction->model()->associate($order);
        //     $transaction->save();
        // });
        DB::commit();
    }

    public function store_orderstock(OrderStock $orderStock)
    {
        $bill_product = $orderStock->stock->billProduct;
        $product_cost = $bill_product->exchange_price - ($bill_product->tax / $bill_product->convert);
        $order_expense = $product_cost * $orderStock->quantity;
    }

    public function store_orderservice($order_service)
    {
        //
    }
}
