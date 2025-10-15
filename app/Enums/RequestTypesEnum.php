<?php

namespace App\Enums;

enum RequestTypesEnum: int
{
    case TIME = 1;
    case CANCEL = 2;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
