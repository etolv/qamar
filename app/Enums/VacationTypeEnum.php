<?php

namespace App\Enums;

enum VacationTypeEnum: int
{
    case ANNUAL = 1;
    case SICK = 2;
    case UNPAID = 3;
    case PUBLIC_HOLIDAY = 4;
    case PERMISSION = 5;
    case OTHER = 6;
    case ROUNDED = 7;
    case CASHED = 8;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
