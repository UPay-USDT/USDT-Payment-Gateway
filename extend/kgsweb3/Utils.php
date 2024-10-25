<?php

namespace kgsweb3;

use kornrunner\Keccak;
use stdClass;

class Utils
{
    const SHA3_NULL_HASH = 'c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470';

    public static function remove0x($value)
    {
        if (strtolower(substr($value, 0, 2)) == '0x') {
            return substr($value, 2);
        }
        return $value;
    }

    public static function add0x($value): string
    {
        return '0x' . self::remove0x($value);
    }

    public static function pubKeyToAddress($pubkey)
    {
        return '0x' . substr(Keccak::hash(substr(hex2bin($pubkey), 1), 256), 24);
    }

    /**
     * RLPencode
     */
    public static function rawEncode(array $input): string
    {
        $rlp = new \kgsweb3\RLP;
        $data = [];
        foreach ($input as $item) {
            // If the value is invalid: 0, 0x0, list it as an empty string
            $data[] = $item && hexdec(Utils::remove0x($item)) != 0 ? Utils::add0x($item) : '';
        }
        return $rlp->encode($data)->toString('hex');
    }

    /**
     *
     * @param string $str
     * @param int $bit
     * @return string
     */
    public static function fill0($str, $bit = 64)
    {
        if (!strlen($str)) return "";
        $str_len = strlen($str);
        $zero = '';
        for ($i = $str_len; $i < $bit; $i++) {
            $zero .= "0";
        }
        $real_str = $zero . $str;
        return $real_str;
    }

    /**
     * ether to wei
     */
    public static function ethToWei($value, $hex = false)
    {
        $value = bcmul($value, '1000000000000000000');
        if ($hex) {
            return self::decToHex($value, $hex);
        }
        return $value;
    }

    /**
     * wei to ether
     */
    public static function weiToEth($value, $hex = false)
    {
        if (strtolower(substr($value, 0, 2)) == '0x') {
            $value = self::hexToDec(self::remove0x($value));
        }
        $value = bcdiv($value, '1000000000000000000', 18);
        if ($hex) {
            return '0x' . self::decToHex($value);
        }
        return $value;
    }

    /**
     * change to hex(0x)
     * @param string|number $value
     * @param boolean $mark
     * @return string
     */
    public static function decToHex($value, $mark = true)
    {
        $hexvalues = [
            '0', '1', '2', '3', '4', '5', '6', '7',
            '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'
        ];
        $hexval = '';
        while ($value != '0') {
            $hexval = $hexvalues[bcmod($value, '16')] . $hexval;
            $value = bcdiv($value, '16', 0);
        }

        return ($mark ? '0x' . $hexval : $hexval);
    }

    /**
     * change to hex(0x)
     * @param string $number hex number
     * @return string
     */
    public static function hexToDec($number)
    {
        // have 0x,remove it
        $number = self::remove0x(strtolower($number));
        $decvalues = [
            '0' => '0', '1' => '1', '2' => '2',
            '3' => '3', '4' => '4', '5' => '5',
            '6' => '6', '7' => '7', '8' => '8',
            '9' => '9', 'a' => '10', 'b' => '11',
            'c' => '12', 'd' => '13', 'e' => '14',
            'f' => '15'];
        $decval = '0';
        $number = strrev($number);
        for ($i = 0; $i < strlen($number); $i++) {
            $decval = bcadd(bcmul(bcpow('16', $i, 0), $decvalues[$number[$i]]), $decval);
        }
        return $decval;
    }

    /**
     * jsonMethodToString
     *
     * @param stdClass|array $json
     * @return string
     */
    public static function jsonMethodToString($json)
    {
        if ($json instanceof stdClass) {
            // one way to change whole json stdClass to array type
            // $jsonString = json_encode($json);

            // if (JSON_ERROR_NONE !== json_last_error()) {
            //     throw new InvalidArgumentException('json_decode error: ' . json_last_error_msg());
            // }
            // $json = json_decode($jsonString, true);

            // another way to change whole json to array type but need the depth
            // $json = self::jsonToArray($json, $depth)

            // another way to change json to array type but not whole json stdClass
            $json = (array)$json;
            $typeName = [];

            foreach ($json['inputs'] as $param) {
                if (isset($param->type)) {
                    $typeName[] = $param->type;
                }
            }
            return $json['name'] . '(' . implode(',', $typeName) . ')';
        } elseif (!is_array($json)) {
            throw new \Exception('jsonMethodToString json must be array or stdClass.');
        }
        if (isset($json['name']) && strpos($json['name'], '(') > 0) {
            return $json['name'];
        }
        $typeName = [];

        foreach ($json['inputs'] as $param) {
            if (isset($param['type'])) {
                $typeName[] = $param['type'];
            }
        }
        return $json['name'] . '(' . implode(',', $typeName) . ')';
    }

    /**
     * sha3
     * keccak256
     *
     * @param string $value
     * @return string
     */
    public static function sha3($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to sha3 function must be string.');
        }
        if (strpos($value, '0x') === 0) {
            $value = self::hexToBin($value);
        }
        $hash = Keccak::hash($value, 256);
        if ($hash === self::SHA3_NULL_HASH) {
            return null;
        }
        return '0x' . $hash;
    }
    /**
     * hexToBin
     *
     * @param string
     * @return string
     */
    public static function hexToBin($value)
    {
        if (!is_string($value)) {
            throw new \Exception('The value to hexToBin function must be string.');
        }
        if (self::isZeroPrefixed($value)) {
            $count = 1;
            $value = str_replace('0x', '', $value, $count);
        }
        return pack('H*', $value);
    }

    /**
     * isZeroPrefixed
     *
     * @param string
     * @return bool
     */
    public static function isZeroPrefixed($value)
    {
        if (!is_string($value)) {
            throw new \Exception('The value to isZeroPrefixed function must be string.');
        }
        return (strpos($value, '0x') === 0);
    }
}