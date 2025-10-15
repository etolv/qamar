<?php

namespace App\Enums;

enum CashFlowStatusEnum: int
{
    case PENDING = 1;
    case PAID = 2;
    case POSTPONED = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
