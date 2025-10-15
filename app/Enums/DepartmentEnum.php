<?php

namespace App\Enums;

enum DepartmentEnum: int
{
    case SALON = 1;
    case CAFETERIA = 2;

    public static function fromName($name)
    {
        return constant("self::$name");
    }

    public static function fromValue(int $value): self
    {
        return match ($value) {
            self::SALON->value => self::SALON,
            self::CAFETERIA->value => self::CAFETERIA,
            default => throw new \InvalidArgumentException("Invalid value: $value"),
        };
    }
}
