<?php

namespace App\Enums;

enum AttendanceStatusEnum: int
{
    case NORMAL = 1;
    case LATE = 2;
    case LEAVE_EARLY = 3;
    case NO_C_IN = 4;
    case NO_C_OUT = 5;
    case ABSENT = 6;
    case VACATION = 7;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
