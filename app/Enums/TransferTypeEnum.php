<?php

namespace App\Enums;

enum TransferTypeEnum: int
{
    case BUY = 1;
    case SELL = 2;
    case RETURN = 3;
    case CANCEL = 4;
    case OTHER = 5;
    case TRANSFER = 6;
    case CUSTODY = 7;
    case WASTE = 8;
    case CONSUMPTION = 9;
    case EXCHANGE = 10;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
