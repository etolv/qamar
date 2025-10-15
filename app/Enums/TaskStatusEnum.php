<?php

namespace App\Enums;

enum TaskStatusEnum: int
{
    case PENDING = 1;
    case STARTED = 2;
    case COMPLETED = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
