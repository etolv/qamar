<?php

return [
    'order' => [
        'from' => 'client',
        'to' => 'sales'
    ],
    'order_expense' => [
        'from' => 'sales_expense',
        'to' => 'warehouse'
    ],
    'stock_expense' => [
        'from' => 'employee',
        'to' => 'warehouse'
    ],
    "order_payment" => [
        'from' => 'cash',
        'to' => 'client'
    ],
    "order_tax" => [
        "from" => "client",
        "to" => "collected_tax"
    ],
    "order_return_tax" => [
        "from" => "collected_tax",
        "to" => "client"
    ],
    'order_return' => [
        'from' => 'sales',
        'to' => 'client'
    ],
    "order_return_payment" => [
        'from' => 'client',
        'to' => 'cash'
    ],
    'bill_return' => [
        'from' => 'supplier',
        'to' => 'warehouse'
    ],
    "bill_return_tax" => [
        "from" => "supplier",
        "to" => "paid_tax"
    ],
    "bill_return_payment" => [
        'from' => 'cash',
        'to' => 'supplier'
    ],
    'bill' => [
        'from' => 'warehouse',
        'to' => 'supplier'
    ],
    'expense' => [
        'from' => 'general_expense',
        'to' => 'supplier'
    ],
    "bill_payment" => [
        'from' => 'supplier',
        'to' => 'cash'
    ],
    "bill_tax" => [
        "from" => "paid_tax",
        "to" => "supplier"
    ],
    'payments_type' => [
        'CASH' => 'cash',
        'BANK' => 'bank',
        'ONLINE' => 'bank',
    ],
    'stock_withdrawal' => [
        'from' => 'withdrawal_account',
        'to' => 'warehouse',
    ],
    'withdrawal_account' => [
        'WASTE' => 'waste_expense',
        'CONSUMPTION' => 'consumption_expense',
        'EXCHANGE' => 'employee'
    ],
    'cash_flow' => [
        'from' => 'employee',
        'to' => 'cash',
    ],
    'salary' => [
        'from' => 'employee',
        'to' => 'salary_expense',
    ]
];
