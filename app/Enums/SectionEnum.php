<?php

namespace App\Enums;

enum SectionEnum: int
{
    case HR = 1;
    case FINANCIAL = 2;
    case MANAGEMENT = 3;
    case STAFF = 4;
    case PROCUREMENT = 5;
    case SALES = 6;
    case WAREHOUSE = 7;
    case CAFETERIA = 8;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
