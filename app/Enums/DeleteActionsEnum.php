<?php

namespace App\Enums;


enum DeleteActionsEnum: string
{
    case SOFT_DELETE = "SOFT_DELETE";
    case FORCE_DELETE = "FORCE_DELETE";
    case RESTORE_DELETED = "RESTORE_DELETED";
    public static function typeOf($type)
    {
        return self::$type();
    }
    public static function SOFT_DELETE(): DeleteActionsEnum
    {
        return self::SOFT_DELETE;
    }
    public static function FORCE_DELETE(): DeleteActionsEnum
    {
        return self::FORCE_DELETE;
    }
    public static function RESTORE_DELETED(): DeleteActionsEnum
    {
        return self::RESTORE_DELETED;
    }
}
