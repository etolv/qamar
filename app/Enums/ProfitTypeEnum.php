<?php

namespace App\Enums;

enum ProfitTypeEnum: int
{
    case PRODUCT = 1;
    case SERVICE = 2;
    case SALON_ORDER = 3;
    case CAFETERIA_ORDER = 4;
    case ALL_ORDER = 5;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
