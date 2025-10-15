<?php

namespace App\Enums;

enum CustodyStatusEnum: int
{
    case USING = 1;
    case RETURNED = 2;
    case WASTED = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
