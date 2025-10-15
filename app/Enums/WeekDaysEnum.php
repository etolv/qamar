<?php

namespace App\Enums;

enum WeekDaysEnum: int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
