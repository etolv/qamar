<?php

namespace App\Enums;

enum TripStatusEnum: int
{
    case PENDING = 1;
    case STARTED = 2;
    case CANCELED = 3;
    case COMPLETED = 4;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
