<?php

return [

    //TODO : This for first time seeder
    'role_structure' => [

        'super_admin' => [
            'dashboard' => 'r',

            'hr_tab' => 'r',
            'shift' => 'c,r,u,d,e',
            'attendance' => 'c,r,u,d,e',
            'salary' => 'c,r,u,d,e',
            'generated_salary' => 'c,r,u,d,e',
            'vacation' => 'c,r,u,d,e',

            'financial_tab' => 'r',
            'account' => 'c,r,u,d,e',
            'transaction' => 'c,r,u,d,e',
            'payment' => 'c,r,u,d,e',
            'cash_flow' => 'c,r,u,d,e',

            'system_tab' => 'r',
            'translation' => 'c,r,u,d,e',
            'model_record' => 'c,r,u,d,e',

            'driver_tab' => 'r',
            'driver' => 'c,r,u,d,e',
            'trip' => 'c,r,u,d,e',
            'customer_tab' => 'r',
            'customer' => 'c,r,u,d,e',

            'employee_tab' => 'r',
            'role' => 'c,r,u,d,e',
            'employee' => 'c,r,u,d,e',
            'admin' => 'c,r,u,d,e',
            'task' => 'c,r,u,d,e',
            'job' => 'c,r,u,d,e',
            'nationality' => 'c,r,u,d,e',

            'setting_tab' => 'r',
            'state' => 'c,r,u,d,e',
            'city' => 'c,r,u,d,e',
            'municipal' => 'c,r,u,d,e',
            'setting' => 'c,r,u,d',
            'notification' => 'c,r,u,d,e',
            'branch' => 'c,r,u,d,e',
            'faq' => 'c,r,u,d,e',
            'slider' => 'c,r,u,d,e',

            'marketing_tab' => 'r',
            'package' => 'c,r,u,d,e',
            'coupon' => 'c,r,u,d,e',
            'loyalty' => 'c,r,u,d,e',

            'stock_tab' => 'r',
            'supplier' => 'c,r,u,d,e',
            'expense_type' => 'c,r,u,d,e',
            'bill' => 'c,r,u,d,e',
            'bill_dept' => 'c,r,u,d,e',
            'bill_type' => 'c,r,u,d,e',
            'bill_return' => 'c,r,u,d,e',
            'product' => 'c,r,u,d,e',
            'stock' => 'c,r,u,d,e',
            'stock_withdrawal' => 'c,r,u,d,e',
            'custody' => 'c,r,u,d,e',
            'transfer' => 'c,r,u,d,e',
            'brand' => 'c,r,u,d,e',
            'unit' => 'c,r,u,d,e',

            'service_tab' => 'r',
            'category' => 'c,r,u,d,e',
            'card' => 'c,r,u,d,e',
            'service' => 'c,r,u,d,e',

            'booking_tab' => 'r',
            'booking' => 'c,r,u,d,e',
            'booking_edit_request' => 'c,r,u,d,e',
            'booking_cancel_request' => 'c,r,u,d,e',

            'report_tab' => 'r',
            'order_report' => 'r',
            'service_report' => 'r',
            'employee_report' => 'r',
            'supplier_report' => 'r',
            'expense_report' => 'r',
            'trial_report' => 'r',
            'financial_report' => 'r',

            'order_tab' => 'r',
            'order' => 'c,r,u,d,e',
            'order_service_return' => 'c,r,u,d,e',
            'order_service_postpone' => 'c,r,u,d,e',
            'listed_order' => 'c,r,u,d,e',
            'rate' => 'c,r,u,d,e',
            'rate_reason' => 'c,r,u,d,e',

            'cafeteria_tab' => 'r',
            'cafeteria_order' => 'c,r,u,d,e',
        ]
    ],


    //TODO : This for new permission you want to add , Please notice after you add the permission here and after run the db seed of it , please transfer the permission to up to role_structure
    'role_update_structure' => [
        'super_admin' => [
            'dashboard' => 'r',

            'hr_tab' => 'r',
            'shift' => 'c,r,u,d,e',
            'attendance' => 'c,r,u,d,e',
            'salary' => 'c,r,u,d,e',
            'generated_salary' => 'c,r,u,d,e',

            'financial_tab' => 'r',
            'account' => 'c,r,u,d,e',
            'transaction' => 'c,r,u,d,e',
            'payment' => 'c,r,u,d,e',
            'cash_flow' => 'c,r,u,d,e',

            'system_tab' => 'r',
            'translation' => 'c,r,u,d,e',
            'model_record' => 'c,r,u,d,e',

            'driver_tab' => 'r',
            'driver' => 'c,r,u,d,e',
            'trip' => 'c,r,u,d,e',
            'customer_tab' => 'r',
            'customer' => 'c,r,u,d,e',

            'employee_tab' => 'r',
            'role' => 'c,r,u,d,e',
            'employee' => 'c,r,u,d,e',
            'admin' => 'c,r,u,d,e',
            'task' => 'c,r,u,d,e',
            'job' => 'c,r,u,d,e',
            'nationality' => 'c,r,u,d,e',

            'setting_tab' => 'r',
            'state' => 'c,r,u,d,e',
            'city' => 'c,r,u,d,e',
            'municipal' => 'c,r,u,d,e',
            'setting' => 'c,r,u,d',
            'notification' => 'c,r,u,d,e',
            'branch' => 'c,r,u,d,e',
            'faq' => 'c,r,u,d,e',
            'slider' => 'c,r,u,d,e',

            'marketing_tab' => 'r',
            'package' => 'c,r,u,d,e',
            'coupon' => 'c,r,u,d,e',
            'loyalty' => 'c,r,u,d,e',

            'stock_tab' => 'r',
            'supplier' => 'c,r,u,d,e',
            'expense_type' => 'c,r,u,d,e',
            'bill' => 'c,r,u,d,e',
            'bill_return' => 'c,r,u,d,e',
            'product' => 'c,r,u,d,e',
            'stock' => 'c,r,u,d,e',
            'stock_withdrawal' => 'c,r,u,d,e',
            'custody' => 'c,r,u,d,e',
            'transfer' => 'c,r,u,d,e',
            'brand' => 'c,r,u,d,e',
            'unit' => 'c,r,u,d,e',

            'service_tab' => 'r',
            'category' => 'c,r,u,d,e',
            'service' => 'c,r,u,d,e',

            'booking_tab' => 'r',
            'booking' => 'c,r,u,d,e',
            'booking_edit_request' => 'c,r,u,d,e',
            'booking_cancel_request' => 'c,r,u,d,e',

            'report_tab' => 'r',

            'order_tab' => 'r',
            'order' => 'c,r,u,d,e',
            'order_service_return' => 'c,r,u,d,e',
            'order_service_postpone' => 'c,r,u,d,e',
            'listed_order' => 'c,r,u,d,e',
            'rate' => 'c,r,u,d,e',
            'rate_reason' => 'c,r,u,d,e',

            'cafeteria_tab' => 'r',
            'cafeteria_order' => 'c,r,u,d,e',
        ]
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
        'e' => 'export'
    ]
];
