<?php

namespace App\Enums;

enum RateTypeEnum: int
{
    case SERVICE = 1;
    case PRODUCT = 2;
    case EMPLOYEE = 3;
    case SUPPORT = 4;
    case APP = 5;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
