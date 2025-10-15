<?php

namespace App\Enums;

enum ItemTypeEnum: int
{
    case NORMAL = 1;
    case PACKAGE = 2;
    case GIFT = 3;
    case SERVICE = 4;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
