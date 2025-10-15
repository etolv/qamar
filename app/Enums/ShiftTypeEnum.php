<?php

namespace App\Enums;

enum ShiftTypeEnum: int
{
    case MORNING = 1;
    case EVENING = 2;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
