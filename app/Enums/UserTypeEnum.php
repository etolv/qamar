<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    case CUSTOMER = 1;
    case COMPANY = 2;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
