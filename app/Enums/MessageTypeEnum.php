<?php
  
namespace App\Enums;
 
enum MessageTypeEnum:string {
    case TEXT = 'TEXT';
    case IMAGE = 'IMAGE';
    case AUDIO = 'AUDIO';
    case FILE = 'FILE';
    case CALL = 'CALL';
    case BOOKING = 'BOOKING';
    case INVOICE = 'INVOICE';
}
