<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                "slug" => "assets",
                "name" => "الاصول",
                "is_debit" => true,
                "accounts" => [
                    [
                        "slug" => "static_assets",
                        "name" => "الاصول الثابتة",
                        "is_debit" => true
                    ],
                    [
                        "slug" => "movable_assets",
                        "name" => "الاصول المتداولة",
                        "is_debit" => true,
                        "accounts" => [
                            [
                                "slug" => "cash",
                                "name" => "النقدية",
                                "is_debit" => true
                            ],
                            [
                                "slug" => "bank",
                                "name" => "البنوك",
                                "is_debit" => true
                            ],
                            [
                                "slug" => "warehouse",
                                "name" => "المستودعات",
                                "is_debit" => true
                            ],
                            [
                                "slug" => "client",
                                "name" => "العملاء",
                                "is_debit" => true
                            ],
                            [
                                "slug" => "custodies",
                                "name" => "العهد",
                                "is_debit" => true
                            ],
                            [
                                "slug" => "procurement",
                                "name" => "المشتريات",
                                "is_debit" => true
                            ],
                            [
                                "slug" => "employee",
                                "name" => "الموظفون",
                                "is_debit" => true
                            ]
                        ]
                    ]
                ]
            ],
            [
                "slug" => "liabilities",
                "name" => "المديونية",
                "is_debit" => false,
                "accounts" => [
                    [
                        "slug" => "supplier",
                        "name" => "الموردين",
                        "is_debit" => false
                    ],
                    [
                        "slug" => "collected_tax",
                        "name" => "الضريبة المحصلة",
                        "is_debit" => false
                    ],
                    [
                        "slug" => "paid_tax",
                        "name" => "الضريبة المدفوعة",
                        "is_debit" => false
                    ]
                ]
            ],
            [
                "slug" => "equity",
                "name" => "المالكية",
                "is_debit" => false,
                "accounts" => [
                    [
                        "slug" => "owner",
                        "name" => "المالك",
                        "is_debit" => false
                    ],
                    [
                        "slug" => "profit",
                        "name" => "الربح",
                        "is_debit" => false
                    ],
                    [
                        "slug" => "loss",
                        "name" => "الخسارة",
                        "is_debit" => false
                    ]
                ]
            ],
            [
                "slug" => "revenue",
                "name" => "الايرادات",
                "is_debit" => false,
                "accounts" => [
                    [
                        "slug" => "sales",
                        "name" => "المبيعات",
                        "is_debit" => false
                    ],
                    [
                        "slug" => "sales_return",
                        "name" => "مرتجع المبيعات",
                        "is_debit" => false
                    ]
                ]
            ],
            [
                "slug" => "expenses",
                "name" => "المصروفات",
                "is_debit" => true,
                "accounts" => [
                    [
                        "slug" => "sales_expense",
                        "name" => "تكلفة المبيعات",
                        "is_debit" => true
                    ],
                    [
                        "slug" => "salary_expense",
                        "name" => "تكلفة الرواتب",
                        "is_debit" => true
                    ],
                    [
                        "slug" => "purchase_expense",
                        "name" => "مصروفات المشتريات",
                        "is_debit" => true
                    ],
                    [
                        "slug" => "general_expense",
                        "name" => "مصروفات عامة",
                        "is_debit" => true
                    ],
                    [
                        "slug" => "waste_expense",
                        "name" => "مصروفات اهلاك",
                        "is_debit" => true
                    ],
                    [
                        "slug" => "consumption_expense",
                        "name" => "مصروفات استهلاك",
                        "is_debit" => true
                    ],
                    [
                        "slug" => "transport_expense",
                        "name" => "مصروفات النقل",
                        "is_debit" => true
                    ]
                ]
            ]
        ];
        DB::beginTransaction();
        $count = 0;
        $created_accounts = $this->create($accounts, null);
        DB::commit();
        $this->command->info('Created');
    }

    public function create($accounts, $account_id = null)
    {
        foreach ($accounts as $account) {
            $account['account_id'] = $account_id;
            $account_model = Account::updateOrCreate(['slug' => $account['slug']], Arr::except($account, ['accounts']));
            $this->command->info('Creating Account ' . $account_model->slug);
            if (isset($account['accounts'])) {
                $this->command->info('Creating Sub Accounts for ' . $account_model->slug);
                $this->create($account['accounts'], $account_model->id);
            }
        }
    }
}
