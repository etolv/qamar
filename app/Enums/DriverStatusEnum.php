<?php
  
namespace App\Enums;
 
enum DriverStatusEnum:int {
    case ONLINE = 1;
    case OFFLINE = 2;
    case BUSY = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
