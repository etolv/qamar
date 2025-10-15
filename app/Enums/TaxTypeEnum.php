<?php

namespace App\Enums;

enum TaxTypeEnum: int
{
    case TAXED = 1;
    case EXEMPTED = 2;
    case ZEROED = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
