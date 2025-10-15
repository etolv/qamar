<?php

namespace App\Enums;

enum OrderableTypeEnum: int
{
    case CAFETERIA = 1;
    case CUSTOMER = 2;
    case EMPLOYEE = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
