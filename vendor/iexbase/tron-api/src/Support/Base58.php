<?php
namespace IEXBase\TronAPI\Support;

class Base58
{
    /**
     * Encodes the passed whole string to base58.
     *
     * @param $num
     * @param int $length
     *
     * @return string
     */
    public static function encode($num, $length = 58): string
    {
        return Crypto::dec2base($num, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    /**
     * Base58 decodes a large integer to a string.
     *
     * @param string $addr
     * @param int $length
     *
     * @return string
     */
    public static function decode(string $addr, int $length = 58): string
    {
        return Crypto::base2dec($addr, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    /**
     * Base58 bc2bin a large integer to a string.
     *
     * @param string $addr
     * @param int $length
     *
     * @return string
     */
    public static function bc2bin(string $addr, int $length = 1): string
    {
        return substr('1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',$addr,$length);
    }

    /**
     * Base58 bc2bin a large integer to a string.
     *
     * @param string $addr
     * @param int $length
     *
     * @return string
     */
    public static function bin2bc(string $addr, int $length = 1): string
    {
        return strpos('1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',$addr,$length);
    }
}
