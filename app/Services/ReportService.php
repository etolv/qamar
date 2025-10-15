<?php

namespace App\Services;

use App\Enums\BillTypeEnum;
use App\Enums\CashFlowTypeEnum;
use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Account;
use App\Models\Bill;
use App\Models\BillReturn;
use App\Models\Booking;
use App\Models\CafeteriaOrder;
use App\Models\CashFlow;
use App\Models\GeneratedSalary;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\OrderServiceReturn;
use App\Models\Payment;
use App\Models\StockWithdrawal;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * Class ReportService.
 */
class ReportService
{
    public function finance($data = [])
    {
        $queryFilter = function ($query) use ($data) {
            if (isset($data['from'])) {
                $query->where('created_at', '>=', $data['from']);
            }
            if (isset($data['to'])) {
                $query->where('created_at', '<=', $data['to']);
            }
        };
        $orderSales = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::ORDER_SALE->value)->sum('amount');
        $bookingSales = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::BOOKING_SALE->value)->sum('amount');

        $totalSales = $orderSales + $bookingSales;

        $orderExpenses = Transaction::where($queryFilter)->where(function ($query) {
            $query->where('type', TransactionTypeEnum::ORDER_WAREHOUSE_EXPENSE->value)
                ->orWhere('type', TransactionTypeEnum::ORDER_EMPLOYEE_EXPENSE->value);
        })->sum('amount');
        $bookingExpenses = Transaction::where($queryFilter)->where(function ($query) {
            $query->where('type', TransactionTypeEnum::BOOKING_WAREHOUSE_EXPENSE->value)
                ->orWhere('type', TransactionTypeEnum::BOOKING_EMPLOYEE_EXPENSE->value);
        })->sum('amount');

        $totalExpenses = Transaction::where($queryFilter)->where(function ($query) {
            $query->where('type', TransactionTypeEnum::ORDER_EMPLOYEE_EXPENSE->value)
                ->orWhere('type', TransactionTypeEnum::ORDER_WAREHOUSE_EXPENSE->value)
                ->orWhere('type', TransactionTypeEnum::BOOKING_EMPLOYEE_EXPENSE->value)
                ->orWhere('type', TransactionTypeEnum::BOOKING_WAREHOUSE_EXPENSE->value)
                ->orWhere('type', TransactionTypeEnum::STOCK_WITHDRAWAL->value)
                ->orWhere('type', TransactionTypeEnum::BILL_EXPENSE->value);
        })->sum('amount');

        $totalPurchases = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::BILL_PURCHASE->value)
            ->sum('amount');
        $salaryExpenses = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::SALARY->value)->sum('amount');

        $totalExpenses = $totalExpenses + $orderExpenses + $bookingExpenses;

        $profit = $totalSales - ($totalExpenses + $totalPurchases);
        return [
            'orderSales' => $orderSales,
            'bookingSales' => $bookingSales,
            'totalSales' => $totalSales,
            'orderExpenses' => $orderExpenses,
            'bookingExpenses' => $bookingExpenses,
            'totalExpenses' => $totalExpenses,
            'totalPurchases' => $totalPurchases,
            'profit' => $profit,
            'salaryExpenses' => $salaryExpenses
        ];
    }

    public function paymentSummary($data = [])
    {
        $queryFilter = function ($query) use ($data) {
            if (isset($data['from'])) {
                $query->where('transactions.created_at', '>=', $data['from']);
            }
            if (isset($data['to'])) {
                $query->where('transactions.created_at', '<=', $data['to']);
            }
        };

        $cafeteriaTransactions = Transaction::selectRaw('cards.name as card_name, SUM(transactions.amount) as total_amount')
            ->join('payments', function ($join) {
                $join->on('transactions.model_id', '=', 'payments.id')
                    ->where('transactions.model_type', '=', Payment::class);
            })->leftjoin('cards', 'payments.card_id', '=', 'cards.id')
            ->where($queryFilter)
            ->whereIn('transactions.type', [
                TransactionTypeEnum::CAFETERIA_ORDER_PAYMENT->value
            ])
            ->groupBy('payments.card_id', 'cards.name')
            ->get();
        $orderTransactions = Transaction::selectRaw('cards.name as card_name, SUM(transactions.amount) as total_amount')
            ->join('payments', function ($join) {
                $join->on('transactions.model_id', '=', 'payments.id')
                    ->where('transactions.model_type', '=', Payment::class);
            })->leftjoin('cards', 'payments.card_id', '=', 'cards.id')
            ->where($queryFilter)
            ->whereIn('transactions.type', [
                TransactionTypeEnum::ORDER_PAYMENT->value
            ])
            ->groupBy('payments.card_id', 'cards.name')
            ->get();
        $bookingTransactions = Transaction::selectRaw('cards.name as card_name, SUM(transactions.amount) as total_amount')
            ->join('payments', function ($join) {
                $join->on('transactions.model_id', '=', 'payments.id')
                    ->where('transactions.model_type', '=', Payment::class);
            })->leftjoin('cards', 'payments.card_id', '=', 'cards.id')
            ->where($queryFilter)
            ->whereIn('transactions.type', [
                TransactionTypeEnum::BOOKING_PAYMENT->value
            ])
            ->groupBy('payments.card_id', 'cards.name')
            ->get();
        $purchaseTransactions = Transaction::selectRaw('cards.name as card_name, SUM(transactions.amount) as total_amount')
            ->join('payments', function ($join) {
                $join->on('transactions.model_id', '=', 'payments.id')
                    ->where('transactions.model_type', '=', Payment::class);
            })->leftjoin('cards', 'payments.card_id', '=', 'cards.id')
            ->where($queryFilter)
            ->whereIn('transactions.type', [
                TransactionTypeEnum::BILL_PURCHASE_PAYMENT->value
            ])
            ->groupBy('payments.card_id', 'cards.name')
            ->get();
        $expensesTransactions = Transaction::selectRaw('cards.name as card_name, SUM(transactions.amount) as total_amount')
            ->join('payments', function ($join) {
                $join->on('transactions.model_id', '=', 'payments.id')
                    ->where('transactions.model_type', '=', Payment::class);
            })->leftjoin('cards', 'payments.card_id', '=', 'cards.id')
            ->where($queryFilter)
            ->whereIn('transactions.type', [
                TransactionTypeEnum::BILL_EXPENSE_PAYMENT->value
            ])
            ->groupBy('payments.card_id', 'cards.name')
            ->get();

        return [
            'cafeteria' => $cafeteriaTransactions,
            'orders' => $orderTransactions,
            'bookings' => $bookingTransactions,
            'purchases' => $purchaseTransactions,
            'expenses' => $expensesTransactions
        ];
    }

    public function revenue($data = [])
    {
        $queryFilter = function ($query) use ($data) {
            if (isset($data['from'])) {
                $query->where('created_at', '>=', $data['from']);
            }
            if (isset($data['to'])) {
                $query->where('created_at', '<=', $data['to']);
            }
        };
        $orderSales = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::ORDER_SALE->value)->sum('amount');
        $bookingSales = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::BOOKING_SALE->value)->sum('amount');
        $cafeteriaSales = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::CAFETERIA_ORDER_SALE->value)->sum('amount');

        $orderTax = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::ORDER_TAX->value)->sum('amount');
        $bookingTax = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::BOOKING_TAX->value)->sum('amount');
        $cafeteriaTax = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::CAFETERIA_ORDER_TAX->value)->sum('amount');

        $orderReturns = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::ORDER_RETURN->value)->sum('amount');
        $orderReturnTax = Transaction::where($queryFilter)->where('type', TransactionTypeEnum::ORDER_RETURN_TAX->value)->sum('amount');


        return [
            'orderSales' => $orderSales,
            'bookingSales' => $bookingSales,
            'cafeteriaSales' => $cafeteriaSales,
            'orderReturns' => $orderReturns,
            'orderTax' => $orderTax,
            'bookingTax' => $bookingTax,
            'cafeteriaTax' => $cafeteriaTax,
            'orderReturnTax' => $orderReturnTax
        ];
    }

    public function getTrialBalance(array $data)
    {
        $accounts = Account::all();

        $trialBalance = $accounts->map(function ($account) {
            $debit = Transaction::where('to_account_id', $account->id)->sum('amount');
            $credit = Transaction::where('from_account_id', $account->id)->sum('amount');

            return [
                'account_name' => $account->name,
                'debit' => $debit,
                'credit' => $credit
            ];
        });
        return $trialBalance;
    }

    public function getFinancialReport(array $data)
    {
        $data = array();
        $data['Revenue']['Cafeteria'] = CafeteriaOrder::sum(DB::raw('grand_total - tax'));
        return $data;
    }

    public function expenses($data = [])
    {
        return
            Bill::when(isset($data['from']), function ($query) use ($data) {
                $query->where('created_at', '>=', $data['from']);
            })->when(isset($data['to']), function ($query) use ($data) {
                $query->where('created_at', '<=', $data['to']);
            })->where('type', BillTypeEnum::EXPENSE->value)
            ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->groupBy(['bill_types.name', 'term', 'department'])
            ->select(
                'bill_types.name',
                'term',
                'department',
                DB::raw('sum(total) as total'),
                DB::raw('sum(tax) as tax'),
                DB::raw('count(term) as count')
            )->get();
    }

    public function purchases($data = [])
    {
        return
            Bill::when(isset($data['from']), function ($query) use ($data) {
                $query->where('created_at', '>=', $data['from']);
            })->when(isset($data['to']), function ($query) use ($data) {
                $query->where('created_at', '<=', $data['to']);
            })->where('type', BillTypeEnum::PURCHASE->value)
            // ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->groupBy(['department'])
            ->select(
                'department',
                DB::raw('sum(total) as total'),
                DB::raw('sum(tax) as tax'),
                DB::raw('count(department) as count')
            )->get();
    }

    public function withdrawals($data = [])
    {
        return StockWithdrawal::when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })
            // ->where('type', BillTypeEnum::PURCHASE->value)
            // ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->groupBy(['department', 'type'])
            ->select(
                'department',
                'type',
                DB::raw('sum(price * quantity) as total'),
                DB::raw('sum(tax) as tax'),
                DB::raw('count(type) as count_type')
            )->get();
    }

    public function cash_flows($data = [])
    {
        return CashFlow::when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->where('type', CashFlowTypeEnum::EXPENSE->value)
            // ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->groupBy(['type'])
            ->select(
                'type',
                DB::raw('sum(amount) as total'),
                DB::raw('count(type) as count')
            )->get();
    }

    public function salaries($data = [])
    {
        return GeneratedSalary::when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->groupBy(['month'])
            ->select(
                'month',
                DB::raw('sum(total) as total'),
                DB::raw('count(month) as count')
            )->get();
    }
    public function orders($data = [])
    {
        return
            Order::when(isset($data['from']), function ($query) use ($data) {
                $query->where('created_at', '>=', $data['from']);
            })->when(isset($data['to']), function ($query) use ($data) {
                $query->where('created_at', '<=', $data['to']);
            })
            // ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->groupBy(['department'])
            ->select(
                'department',
                DB::raw('sum(grand_total - tax) as total'),
                DB::raw('sum(tax) as tax'),
                DB::raw('count(department) as count')
            )->get();
    }

    public function cafeteria_orders($data = [])
    {
        return
            CafeteriaOrder::when(isset($data['from']), function ($query) use ($data) {
                $query->where('created_at', '>=', $data['from']);
            })->when(isset($data['to']), function ($query) use ($data) {
                $query->where('created_at', '<=', $data['to']);
            })
            // ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->groupBy(['type'])
            ->select(
                'type',
                DB::raw('sum(grand_total - tax) as total'),
                DB::raw('sum(tax) as tax'),
                DB::raw('count(type) as count')
            )->get();
    }
    public function bookings($data = [])
    {
        return
            Booking::when(isset($data['from']), function ($query) use ($data) {
                $query->where('created_at', '>=', $data['from']);
            })->when(isset($data['to']), function ($query) use ($data) {
                $query->where('created_at', '<=', $data['to']);
            })->where('status', StatusEnum::COMPLETED->value)
            // ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->groupBy(['status'])
            ->select(
                'status',
                DB::raw('sum(grand_total - tax) as total'),
                DB::raw('sum(tax) as tax'),
                DB::raw('count(status) as count')
            )->get();
    }

    public function orderServiceReturns($data = [])
    {
        return
            OrderServiceReturn::when(isset($data['from']), function ($query) use ($data) {
                $query->where('created_at', '>=', $data['from']);
            })->when(isset($data['to']), function ($query) use ($data) {
                $query->where('created_at', '<=', $data['to']);
            })->leftjoin('order_service', 'order_service_returns.order_service_id', '=', 'order_service.id')
            ->select(
                DB::raw('sum(order_service.price - order_service_returns.quantity) as total'),
                DB::raw('count(order_service.id) as count'),
            )->first();
    }
}
