<?php

namespace App\Enums;

enum PaymentStatusEnum: int
{
    case PENDING = 1;
    case PAID = 2;
    case CANCELED = 3;
    case FAILED = 4;
    case RETURN = 5;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
