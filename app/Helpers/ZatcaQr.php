<?php

namespace App\Helpers;

class ZatcaQr
{
    /**
     * Build ZATCA TLV then return Base64 string
     * @param string $sellerName    اسم المورد
     * @param string $vatNumber     الرقم الضريبي
     * @param string $timestampIso  ISO8601 e.g. 2025-09-18T13:49:18+03:00
     * @param string $totalAmount   الإجمالي شامل الضريبة (2 decimals)
     * @param string $vatAmount     قيمة الضريبة (2 decimals)
     * @return string Base64-encoded TLV payload
     */
    public static function base64(string $sellerName, string $vatNumber, string $timestampIso, string $totalAmount, string $vatAmount): string
    {
        $tlv  = self::tlv(1, $sellerName);
        $tlv .= self::tlv(2, $vatNumber);
        $tlv .= self::tlv(3, $timestampIso);
        $tlv .= self::tlv(4, $totalAmount);
        $tlv .= self::tlv(5, $vatAmount);

        return base64_encode($tlv);
    }

    /**
     * Tag-Length-Value encoder (1-byte tag, 1-byte length, UTF-8 bytes)
     */
    private static function tlv(int $tag, string $value): string
    {
        // Use byte length (not characters) for multi-byte Arabic strings
        $len = strlen($value);
        return chr($tag) . chr($len) . $value;
    }
}
