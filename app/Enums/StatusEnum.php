<?php

namespace App\Enums;

enum StatusEnum: int
{
    case PENDING = 1;
    case ACCEPTED = 2;
    case CONFIRMED = 3;
    case STARTED = 4;
    case REJECTED = 5;
    case CANCELED = 6;
    case COMPLETED = 7;
    case EXPIRED = 8;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
