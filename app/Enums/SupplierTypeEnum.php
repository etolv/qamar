<?php

namespace App\Enums;

enum SupplierTypeEnum: int
{
    case PHYSICAL = 1;
    case ONLINE = 2;
    case CAFETERIA = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
