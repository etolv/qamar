<?php

namespace App\Enums;

enum ServiceStatusEnum: int
{
    case PENDING = 1;
    case POSTPONED = 2;
    case RETURNED = 3;
    case COMPLETED = 4;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
