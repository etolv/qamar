<?php

namespace App\Enums;

enum RequestStatusEnum: int
{
    case PENDING = 1;
    case ACCEPTED = 2;
    case REJECTED = 3;
    case COMPLETED = 5;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
