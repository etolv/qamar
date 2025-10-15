<?php

namespace App\Enums;

enum TransactionTypeEnum: int
{
    case GENERAL = 1;
    case ORDER_SALE = 2;
    case BOOKING_SALE = 3;
    case ORDER_RETURN = 4;
    case BOOKING_RETURN = 5;
    case ORDER_PAYMENT = 6;
    case BOOKING_PAYMENT = 7;
    case ORDER_EMPLOYEE_EXPENSE = 8;
    case BOOKING_EMPLOYEE_EXPENSE = 9;
    case CASH_DEDUCT = 10;
    case CASH_ADVANCE = 11;
    case CASH_EXPENSE = 12;
    case STOCK_WITHDRAWAL = 13;
    case BOOKING_RETURN_TAX = 14;
    case BOOKING_RETURN_PAYMENT = 15;
    case ORDER_RETURN_TAX = 16;
    case ORDER_RETURN_PAYMENT = 17;
    case BILL_RETURN = 18;
    case BILL_RETURN_PAYMENT = 19;
    case BILL_RETURN_TAX = 20;
    case BILL_PURCHASE = 21;
    case BILL_EXPENSE = 22;
    case BILL_PURCHASE_PAYMENT = 23;
    case BILL_EXPENSE_PAYMENT = 24;
    case BILL_PURCHASE_TAX = 25;
    case BILL_EXPENSE_TAX = 26;
    case ORDER_TAX = 27;
    case ORDER_WAREHOUSE_EXPENSE = 28;
    case BOOKING_WAREHOUSE_EXPENSE = 29;
    case CASH_GIFT = 30;
    case BOOKING_TAX = 31;
    case SALARY = 32;
    case CAFETERIA_ORDER_PAYMENT = 33;
    case CAFETERIA_ORDER_SALE = 34;
    case CAFETERIA_ORDER_TAX = 35;
    case CAFETERIA_ORDER_WAREHOUSE_EXPENSE = 36;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
