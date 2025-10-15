<?php

namespace App\Enums;

enum BillTypeEnum: int
{
    case PURCHASE = 1;
    case EXPENSE = 2;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
