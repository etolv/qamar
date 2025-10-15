<?php

namespace App\Enums;

enum StockWithdrawalTypeEnum: int
{
    case CONSUMPTION = 1;
    case WASTE = 2;
    case EXCHANGE = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }

    public static function fromValue(int $value): self
    {
        return match ($value) {
            self::CONSUMPTION->value => self::CONSUMPTION,
            self::WASTE->value => self::WASTE,
            self::EXCHANGE->value => self::EXCHANGE,
            default => throw new \InvalidArgumentException("Invalid value: $value"),
        };
    }
}
