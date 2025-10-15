<?php

namespace App\Enums;

enum ModelLogEnum: int
{
    case CREATE = 1;
    case UPDATE = 2;
    case DELETE = 3;
    case RESTORE = 4;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
