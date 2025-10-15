<?php

namespace App\Enums;

enum ConsumptionTypeEnum: int
{
    case SALE = 1;
    case EXPENSE = 2;
    case BOTH = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
