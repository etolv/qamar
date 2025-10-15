<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\BillTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\VacationStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\FilterEmployeeReportRequest;
use App\Http\Requests\FilterExpenseReportRequest;
use App\Http\Requests\FilterFinancialReportRequest;
use App\Http\Requests\FilterOrderReportRequest;
use App\Http\Requests\FilterPackageRequest;
use App\Http\Requests\FilterServiceReportRequest;
use App\Http\Requests\FilterSupplierReportRequest;
use App\Http\Requests\FilterTrialBalanceRequest;
use App\Models\Account;
use App\Models\Bill;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Package;
use App\Models\Service;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Services\BillReturnService;
use App\Services\BillService;
use App\Services\BookingService;
use App\Services\CafeteriaOrderService;
use App\Services\EmployeeService;
use App\Services\GeneratedSalaryService;
use App\Services\OrderService;
use App\Services\OrderServiceReturnService;
use App\Services\PackageService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{

    public function __construct(
        private ReportService $reportService,
        private GeneratedSalaryService $generatedSalaryService,
        private EmployeeService $employeeService,
        private BillReturnService $billReturnService,
        private BillService $billService,
        private OrderService $orderService,
        private BookingService $bookingService,
        private OrderServiceReturnService $orderServiceReturnService,
        private CafeteriaOrderService $cafeteriaOrderService,
        private PackageService $packageService
    ) {
        $this->middleware('can:read_order_report')->only('order', 'order_fetch');
        $this->middleware('can:read_employee_report')->only('employee', 'employee_fetch');
        $this->middleware('can:read_supplier_report')->only('supplier', 'supplier_fetch');
        $this->middleware('can:read_expense_report')->only('expense', 'expense_fetch');
        $this->middleware('can:read_trial_report')->only('trial');
        $this->middleware('can:read_financial_report')->only('financial');
    }

    public function income(Request $request)
    {
        // Load all accounts with their transactions
        $accounts = Account::with(['fromTransactions', 'toTransactions'])->get();

        // Group accounts by slug for easier access
        $accountsBySlug = $accounts->keyBy('slug');

        // Helper to get balance by slug safely
        $getBalance = function ($slug) use ($accountsBySlug) {
            $account = $accountsBySlug->get($slug);
            return $account ? $account->netBalance() : 0;
        };

        // Helper to sum balances of all accounts under a group of slugs
        $sumBySlugs = function (array $slugs) use ($getBalance) {
            return array_sum(array_map($getBalance, $slugs));
        };

        // Revenue
        $sales = $getBalance('sales');
        $salesReturns = $getBalance('sales_return');
        $netSales = $sales - $salesReturns;

        // Cost of Goods Sold
        $inventoryStart = 0; // TODO: fetch or set from previous period
        $purchases = $getBalance('procurement') + $getBalance('purchase_expense');
        $inventoryEnd = 0;   // TODO: fetch or set from current period

        $cogs = $inventoryStart + $purchases - $inventoryEnd;
        $grossProfit = $netSales - $cogs;

        // Operating Expenses (grouping known expense slugs)
        $expenseSlugs = [
            'marketing_and_advertising_expenses', // old: مصاريف تسويقية ودعائية
            'salaries_and_administrative_fees',   // old: الرواتب والرسوم الإدارية
            'rental_expenses',                    // old: مصاريف الإيجار
            'depreciation_expenses',              // old: مصروف الإهلاك
            'office_service_expenses',
            'office_expenses_and_printing',
            'transport_expense',
            'social_insurance',
            'travel_tickets',
            'government_fees',
            'fees_and_subscriptions',
            'hospitality_expenses',
            'bank_commissions',
            'other_expenses',
        ];
        $expenses = [];
        foreach ($expenseSlugs as $slug) {
            $slugTitle = ucwords(str_replace('_', ' ', $slug));
            $expenses[$slugTitle] = $getBalance($slug);
        }

        $totalOperatingExpenses = array_sum($expenses);
        $operatingIncome = $grossProfit - $totalOperatingExpenses;

        // Non-operating Income/Expenses
        $otherIncome = $getBalance('other_revenues'); // إيرادات أخرى
        $interestExpense = $getBalance('benefits');   // فوائد
        $incomeBeforeTax = $operatingIncome + $otherIncome - $interestExpense;

        $taxExpense = $getBalance('taxes'); // الضرائب
        $netIncome = $incomeBeforeTax - $taxExpense;

        return view('dashboard.report.income', compact(
            'netSales',
            'cogs',
            'grossProfit',
            'expenses',
            'totalOperatingExpenses',
            'operatingIncome',
            'otherIncome',
            'interestExpense',
            'incomeBeforeTax',
            'taxExpense',
            'netIncome'
        ));
    }


    /**
     * Display a listing of the resource.
     */
    public function offer()
    {
        return view('dashboard.report.offer');
    }

    public function offer_fetch(FilterPackageRequest $request): JsonResponse
    {
        $filter_data = $request->afterValidation();
        return DataTables::eloquent(
            Order::whereHas('orderPackages')->with([
                'orderPackages.package',
                'customer.user' => function ($query) {
                    $query->withTrashed();
                },
                'branch.city' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->name}%");
            })->when($request->customer_id, function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })->when($request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })->when($request->package_id, function ($query) use ($request) {
                $query->whereRelation('orderPackages', 'package_id', $request->package_id);
            })->when($request->employee_id, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->whereRelation('orderServices', 'employee_id', $request->employee_id)
                        ->orWere('employee_id', $request->employee_id);
                });
            })->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })->when(isset($filter_data['from']), function ($query) use ($filter_data) {
                $query->where('bills.created_at', '>=', $filter_data['from']);
            })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
                $query->where('bills.created_at', '<=', $filter_data['to']);
            })
        )->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('status_name', function ($item) {
            return _t($item->status?->name ?? '');
        })->toJson();
    }


    public function trial(FilterTrialBalanceRequest $request)
    {
        $data = $request->afterValidation();
        $trialBalance = $this->reportService->getTrialBalance($data);
        return view('dashboard.report.trial', compact('trialBalance'));
    }

    public function ledger(FilterTrialBalanceRequest $request)
    {
        $data = $request->afterValidation();

        $accounts = Account::all();

        $selectedAccount = $request->input('account_id');
        $transactions = Transaction::when($selectedAccount, function ($query) use ($selectedAccount) {
            return $query->where(function ($query) use ($selectedAccount) {
                $query->where('to_account_id', $selectedAccount)
                    ->orWhere('from_account_id', $selectedAccount);
            });
        })->when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', Carbon::parse($data['from'])->startOfDay());
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', Carbon::parse($data['to'])->endOfDay());
        })->orderBy('created_at', 'asc')->get();
        return view('dashboard.report.ledger', compact('accounts', 'transactions', 'selectedAccount'));
    }

    public function finance(FilterFinancialReportRequest $request)
    {
        $data = $request->afterValidation();
        $finance = $this->reportService->finance($data);
        $summary = $this->reportService->paymentSummary($data);
        return view('dashboard.report.finance', compact('finance', 'summary'));
    }

    public function revenue(FilterFinancialReportRequest $request)
    {
        $data = $request->afterValidation();
        $data = $this->reportService->revenue($data);
        return view('dashboard.report.revenue', compact('data'));
    }

    public function financial(FilterFinancialReportRequest $request)
    {
        $data = $request->afterValidation();
        $withdrawals = $this->reportService->withdrawals(data: $data);
        $salaries = $this->reportService->salaries(data: $data);
        $purchases = $this->reportService->purchases(data: $data);
        $cash_flows = $this->reportService->cash_flows(data: $data);
        $expenses = $this->reportService->expenses(data: $data);
        $salaries = $this->generatedSalaryService->all(data: $data, paginated: false);
        $bill_returns = $this->billReturnService->all(data: $data, paginated: false);
        $cafeteria_orders = $this->reportService->cafeteria_orders(data: $data);
        $orders = $this->reportService->orders(data: $data);
        $bookings = $this->reportService->bookings(data: $data);
        $order_return_services = $this->reportService->orderServiceReturns(data: $data);
        // $order_return_services = $this->orderServiceReturnService->all(data: $data, withes: ['orderService'], paginated: false);
        return view('dashboard.report.financial', compact(
            'purchases',
            'expenses',
            'salaries',
            'bill_returns',
            'order_return_services',
            'cafeteria_orders',
            'orders',
            'bookings',
            'withdrawals',
            'cash_flows'
        ));
    }

    public function financialCenter(FilterFinancialReportRequest $request)
    {
        $data = $request->afterValidation();
        $withdrawals = $this->reportService->withdrawals(data: $data);
        $salaries = $this->reportService->salaries(data: $data);
        $purchases = $this->reportService->purchases(data: $data);
        $cash_flows = $this->reportService->cash_flows(data: $data);
        $expenses = $this->reportService->expenses(data: $data);
        $salaries = $this->generatedSalaryService->all(data: $data, paginated: false);
        $bill_returns = $this->billReturnService->all(data: $data, paginated: false);
        $cafeteria_orders = $this->reportService->cafeteria_orders(data: $data);
        $orders = $this->reportService->orders(data: $data);
        $bookings = $this->reportService->bookings(data: $data);
        $order_return_services = $this->reportService->orderServiceReturns(data: $data);
        // $order_return_services = $this->orderServiceReturnService->all(data: $data, withes: ['orderService'], paginated: false);
        return view('dashboard.report.financial_center', compact(
            'purchases',
            'expenses',
            'salaries',
            'bill_returns',
            'order_return_services',
            'cafeteria_orders',
            'orders',
            'bookings',
            'withdrawals',
            'cash_flows'
        ));
    }

    public function employee()
    {
        $employees = $this->employeeService->without_salaries();
        if (count($employees)) {
            session()->put('employee_without_salaries', $employees);
            session()->flash('error', _t('You need to add salary for employees') . " :" . implode(', ', $employees->pluck('user.name')->toArray()));
        } else {
            session()->forget('employee_without_salaries');
        }
        $date = Carbon::now()->subMonths(1)->format('Y-m-d') . ' to ' . Carbon::now()->format('Y-m-d');
        return view('dashboard.report.employees', compact('date'));
    }


    public function employee_fetch(FilterEmployeeReportRequest $request)
    {
        $data = $request->afterValidation();
        if (session()->get('employee_without_salaries'))
            return [];
        $data = DataTables::eloquent(
            Employee::with([
                'branch' => function ($query) {
                    $query->withTrashed();
                },
                'user' => function ($query) {
                    $query->withTrashed();
                },
                'orders' => function ($query) {
                    $query->with('orderStocks', 'orderServices');
                },
                'bookings' => function ($query) {
                    $query->with('bookingProducts', 'bookingServices');
                },
                'job',
                'nationality',
            ])->when($request->nationality_id, function ($query) use ($request) {
                $query->where('nationality_id', $request->nationality_id);
            })->when($request->name, function ($query) use ($request) {
                $query->whereRelation('user', 'name', 'like',  "%$request->name%");
            })->when($request->phone, function ($query) use ($request) {
                $query->whereRelation('user', 'phone', 'like',  "%$request->phone%");
            })->when($request->email, function ($query) use ($request) {
                $query->whereRelation('user', 'email', 'like',  "%$request->email%");
            })->when($request->role_id, function ($query) use ($request) {
                $query->whereRelation('user.roles', 'id', $request->role_id);
            })->when($request->job_id, function ($query) use ($request) {
                $query->where('job_id', $request->job_id);
            })
        )->addColumn('employee_image', function ($item) {
            return $item->user?->getFirstMedia('profile') ? $item->user?->getFirstMedia('profile')->getUrl() : null;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('holiday', function ($item) {
            return _t($item->holiday?->name);
        })->addColumn('role', function ($item) {
            $role = $item->user->roles()->first();
            return $role?->name;
        })->addColumn('phone', function ($item) {
            return "{$item->user->dial_code} {$item->user->phone}";
        })->addColumn('taken_vacations', function ($item) use ($data) {
            return $item->vacations()->where('start_date', '>=', $data['start'])
                ->where('end_date', '<=', $data['end'])
                ->where('status', VacationStatusEnum::APPROVED->value)->sum('days');
        })->addColumn('salary_data', function ($item) use ($data) {
            return $this->generatedSalaryService->generateSalary($item->id, $data);
        })->toJson();
        return $data;
    }

    public function supplier()
    {
        // $date = Carbon::now()->subMonths(1)->format('Y-m-d') . ' to ' . Carbon::now()->format('Y-m-d');
        return view('dashboard.report.suppliers');
    }

    public function supplier_fetch(FilterSupplierReportRequest $request)
    {
        $filter_data = $request->afterValidation();
        $data = DataTables::eloquent(
            Supplier::with([
                'bills',
                'supplier' => function ($query) {
                    $query->withTrashed();
                },
                'city' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like',  "%$request->name%");
            })->when($request->phone, function ($query) use ($request) {
                $query->where('phone', 'like',  "%$request->phone%");
            })->when($request->email, function ($query) use ($request) {
                $query->where('email', 'like',  "%$request->email%");
            })->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id',  $request->supplier_id);
            })->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
        )->addColumn('profile_image', function ($item) {
            return $item->getFirstMedia('profile') ? $item->getFirstMedia('profile')->getUrl() : null;
        })->addColumn('supplier_image', function ($item) {
            return $item->supplier?->getFirstMedia('profile') ? $item->getFirstMediaUrl('profile') : null;
        })->editColumn('type', function ($item) {
            return _t($item->type?->name);
        })->addColumn('bills_data', function ($item) use ($filter_data) {
            $data = array();
            $data['count'] = $item->bills()->when(isset($filter_data['from']), function ($query) use ($filter_data) {
                $query->where('bills.created_at', '>=', $filter_data['from']);
            })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
                $query->where('bills.created_at', '<=', $filter_data['to']);
            })->count();
            $data['total'] = $item->bills()->when(isset($filter_data['from']), function ($query) use ($filter_data) {
                $query->where('bills.created_at', '>=', $filter_data['from']);
            })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
                $query->where('bills.created_at', '<=', $filter_data['to']);
            })->sum('grand_total');
            $data['paid'] = $item->bills()
                ->join('payments', 'payments.model_id', '=', 'bills.id')
                ->when(isset($filter_data['from']), function ($query) use ($filter_data) {
                    $query->where('bills.created_at', '>=', $filter_data['from']);
                })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
                    $query->where('bills.created_at', '<=', $filter_data['to']);
                })
                ->where('payments.model_type', Bill::class)->sum('payments.amount');
            $data['dept'] = $data['total'] - $data['paid'];
            return $data;
        })->toJson();
        return $data;
    }

    public function expense()
    {
        return view('dashboard.report.expense');
    }

    public function expense_fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Bill::with([
                'transfers',
                'billType',
                'payments',
                'supplier' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->bill_type_id, function ($query) use ($request) {
                $query->where('bill_type_id', $request->bill_type_id);
            })->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })->when($request->term, function ($query) use ($request) {
                $query->where('term', 'like', "%$request->term%");
            })->when($request->date && str_contains($request->date, ' to '), function ($query) use ($request) {
                [$from, $to] = explode(' to ', $request->date);
                $query->whereBetween('created_at', [$from, $to]);
            })->where('type', BillTypeEnum::EXPENSE->value)->latest('bills.id')
        )->addColumn('supplier_image', function ($item) {
            return $item->supplier?->getFirstMedia('profile') ? $item->getFirstMedia('profile')->getUrl() : null;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('receiving_date', function ($item) {
            return Carbon::parse($item->receiving_date)->format('Y-m-d H:i');
        })->editColumn('paid', function ($item) {
            return $item->payments->sum('amount');
        })->addColumn('left', function ($item) {
            return $item->total - $item->payments->sum('amount');
        })->editColumn('department', function ($item) {
            return _t($item->department?->name);
        })->toJson();
        return $data;
    }

    public function order()
    {
        return view('dashboard.report.order');
    }

    public function order_fetch(FilterOrderReportRequest $request)
    {
        $filter_data = $request->afterValidation();
        $query = Order::withTrashed()->with([
            'customer.user' => function ($query) {
                $query->withTrashed();
            },
            'employee.user' => function ($query) {
                $query->withTrashed();
            },
            'branch.city' => function ($query) {
                $query->withTrashed();
            },
            'gifter.user' => function ($query) {
                $query->withTrashed();
            }
        ])->when($request->customer_id, function ($query) use ($request) {
            $query->where('customer_id', $request->customer_id);
        })->when($request->department, function ($query) use ($request) {
            $query->where('department', $request->department);
        })->when(isset($filter_data['from']), function ($query) use ($filter_data) {
            $query->where('created_at', '>=', $filter_data['from']);
        })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
            $query->where('created_at', '<=', $filter_data['to']);
        })->latest();

        $total_count = $query->count();
        $total_orders = $query->sum('total');
        $total_tax = $query->sum('tax');

        $data = DataTables::eloquent($query)->addColumn('customer_image', function ($item) {
            return $item->customer->user->getFirstMediaUrl('profile');
        })->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->addColumn('gifter_image', function ($item) {
            return $item->gifter?->user->getFirstMediaUrl('profile');
        })->editColumn('status', function ($item) {
            return _t($item->status?->name ?? '');
        })->editColumn('payment_status', function ($item) {
            return _t($item->payment_status?->name ?? '');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('sum_stocks', function ($item) {
            return $item->orderStocks()->sum(DB::raw('quantity * price'));
        })->editColumn('sum_services', function ($item) {
            return $item->orderServices()->sum(DB::raw('quantity * price'));
        })->toArray();

        $data['total_count'] = $total_count;
        $data['total_orders'] = $total_orders;
        $data['total_tax'] = $total_tax;
        $bill_query = Bill::when(isset($filter_data['from']), function ($query) use ($filter_data) {
            $query->where('created_at', '>=', $filter_data['from']);
        })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
            $query->where('created_at', '<=', $filter_data['to']);
        });
        $data['bill_count'] = $bill_query->count();
        $data['bill_sum'] = $bill_query->sum('total');
        $data['bill_tax'] = $bill_query->sum('tax');
        return response()->json($data);
    }

    public function service()
    {
        return view('dashboard.report.service');
    }

    public function service_fetch(FilterServiceReportRequest $request)
    {
        $filter_data = $request->afterValidation();
        $query = Service::withTrashed()->with([
            'orderServices',
            'cafeteriaOrderServices',
            'category' => function ($query) {
                $query->withTrashed();
            },
        ])->when($request->department, function ($query) use ($request) {
            $query->where('department', $request->department);
        })->when($request->employee_id, function ($query) use ($filter_data) {
            $query->whereHas('orderServices', function ($query) use ($filter_data) {
                $query->where('employee_id', $filter_data['employee_id']);
            });
        })->when($request->customer_id, function ($query) use ($filter_data) {
            $query->whereHas('orderServices', function ($query) use ($filter_data) {
                $query->whereRelation('order', 'customer_id', $filter_data['customer_id']);
            });
        })->when($request->branch_id, function ($query) use ($filter_data) {
            $query->whereHas('orderServices', function ($query) use ($filter_data) {
                $query->whereRelation('order', 'branch_id', $filter_data['branch_id']);
            });
        })->when(isset($filter_data['from']), function ($query) use ($filter_data) {
            $query->whereHas('orderServices', function ($query) use ($filter_data) {
                $query->where('created_at', '>=', $filter_data['from']);
            });
        })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
            $query->whereHas('orderServices', function ($query) use ($filter_data) {
                $query->where('created_at', '<=', $filter_data['to']);
            });
        })->latest();

        $data = DataTables::eloquent($query)->addColumn('service_image', function ($item) {
            return $item->getFirstMediaUrl('image');
        })->addColumn('total_count', function ($item) {
            if ($item->department == DepartmentEnum::SALON) {
                return $item->orderServices()->count();
            } else {
                return $item->cafeteriaOrderServices()->count();
            }
        })->addColumn('total_sum', function ($item) {
            if ($item->department == DepartmentEnum::SALON) {
                return $item->orderServices()->sum(DB::raw('quantity * price'));
            } else {
                return $item->cafeteriaOrderServices()->sum(DB::raw('quantity * price'));
            }
        })->toArray();

        // $bill_query = Bill::when(isset($filter_data['from']), function ($query) use ($filter_data) {
        //     $query->where('created_at', '>=', $filter_data['from']);
        // })->when(isset($filter_data['to']), function ($query) use ($filter_data) {
        //     $query->where('created_at', '<=', $filter_data['to']);
        // });
        // $data['bill_count'] = $bill_query->count();
        // $data['bill_sum'] = $bill_query->sum('total');
        // $data['bill_tax'] = $bill_query->sum('tax');
        return response()->json($data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
