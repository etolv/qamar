<?php

namespace App\Enums;

enum SettingTypeEnum: int
{
    case STRING = 1;
    case NUMERIC = 2;
    case BOOLEAN = 3;
    case IMAGE = 4;
    case TEXT = 5;
    case DATE = 6;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
