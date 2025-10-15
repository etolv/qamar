<?php

namespace App\Enums;

enum PaymentTypeEnum: int
{
    case CASH = 1;
    case BANK = 2;
    case ONLINE = 3;
    case POINT = 4;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
