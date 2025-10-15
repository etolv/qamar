<?php

namespace App\Enums;

enum VacationStatusEnum: int
{
    case IN_REVIEW = 1;
    case APPROVED = 2;
    case DECLINED = 3;
    case PENDING_REPORT = 4;
    case CANCELED = 5;
    case PENDING = 6;
    case ROUNDED = 7;
    case CASHED = 8;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
