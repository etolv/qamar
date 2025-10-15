<?php

namespace App\Enums;

enum OverTimeStatusEnum: int
{
    case PENDING = 1;
    case OVERTIME = 2;
    case COMPENSATION = 3;
    case REJECTED = 4;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
