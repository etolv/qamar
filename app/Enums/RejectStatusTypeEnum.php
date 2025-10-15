<?php
  
namespace App\Enums;
 
enum RejectStatusTypeEnum:int {
    case DRIVER = 1;
    case CUSTOMER = 2;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
