<?php

namespace App\Enums;

enum ProductTypeEnum: int
{
    case POST = 1;
    case REQUEST = 2;
    case SERVICE = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
