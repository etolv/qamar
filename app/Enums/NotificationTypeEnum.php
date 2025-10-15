<?php

namespace App\Enums;

enum NotificationTypeEnum: int
{
    case NORMAL = 1;
    case TASK = 2;
    case EVENT = 3;
    case MESSAGE = 4;
    case CUSTOM = 5;
    case ADMIN = 6;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
