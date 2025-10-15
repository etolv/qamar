<?php
  
namespace App\Enums;
 
enum WithdrawalRequestStatusEnum:int {
    case PENDING = 1;
    case APPROVED = 2;
    case DECLINED = 3;

    public static function fromName($name)
    {
        return constant("self::$name");
    }
}
