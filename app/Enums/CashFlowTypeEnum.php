<?php

namespace App\Enums;

enum CashFlowTypeEnum: int
{
    case ADVANCE = 1;
    case EXPENSE = 2;
    case DEDUCT = 3;
    case GIFT = 4;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
