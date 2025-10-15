<?php

namespace App\Enums;

enum NotifiableEnum: int
{
    case ALL = 1;
    case EMPLOYEE = 2;
    case CUSTOMER = 3;
    case DRIVER = 4;
    case USER = 5;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
