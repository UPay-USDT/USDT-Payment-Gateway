<?php
/**
 * This file is a part of "comely-io/data-types" package.
 * https://github.com/comely-io/data-types
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/comely-io/data-types/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Comely\DataTypes\BcMath;

use Comely\DataTypes\BcNumber;

/**
 * Class BcMath
 * @package Comely\DataTypes\BcMath
 */
class BcMath
{
    /**
     * Encodes/converts integral numbers (Integer or strings comprised of integral numbers) from Base10 to Base16/Hexadecimal
     * If resulting hexits are not even, this method will prefix "0" to even out
     * @param $decs
     * @param bool $prefixed
     * @return string
     */
    public static function Encode($decs, bool $prefixed = false): string
    {
        if (is_int($decs)) {
            $decs = strval($decs);
        }

        if (!is_string($decs) || !preg_match('/^(0|[1-9]+[0-9]*)$/', $decs)) {
            throw new \InvalidArgumentException('First argument must be an integral number');
        }

        $hexits = BaseConvert::fromBase10(new BcNumber($decs), BaseConvert::CHARSET_BASE16);
        if (strlen($hexits) % 2 !== 0) {
            $hexits = "0" . $hexits; // Even-out resulting hexits
        }

        return $prefixed ? "0x" . $hexits : $hexits;
    }

    /**
     * Converts/decodes from hexadecimals to Base10/decimals
     * @param string $hexits
     * @return string
     */
    public static function Decode(string $hexits): string
    {
        if (!preg_match('/^(0x)?[a-f0-9]+$/i', $hexits)) {
            throw new \InvalidArgumentException('Only hexadecimal numbers can be decoded');
        }

        if (substr($hexits, 0, 2) === "0x") {
            $hexits = substr($hexits, 2);
        }

        return BaseConvert::toBase10String($hexits, BaseConvert::CHARSET_BASE16, false);
    }

    /**
     * Convert arbitrary number between bases
     * @param string $value
     * @param int $fromBase
     * @param int $targetBase
     * @return string
     */
    public static function BaseConvert(string $value, int $fromBase, int $targetBase): string
    {
        if ($fromBase === $targetBase) { // Both from and target bases are, what's the point?
            return $value;
        }

        // Case sensitivity (primarily for Hexadecimals)
        $fromBaseIsCaseSensitive = true;
        if ($fromBase < 36) {
            $fromBaseIsCaseSensitive = false;
        }

        // No need to convert to decimals (Base10) if it already is Base10
        $decs = $fromBase === 10 ?
            new BcNumber($value) : BaseConvert::toBase10($value, BaseConvert::Charset($fromBase), $fromBaseIsCaseSensitive);

        return $targetBase === 10 ? $decs->value() : BaseConvert::fromBase10($decs, BaseConvert::Charset($targetBase));
    }

    /**
     * Returns instance of BcNumber if given argument is a valid numeric value of any data type,
     * otherwise returns NULL without throwing any Exception. This method may be used in IF statements to check if
     * argument is a valid number (of any data type)
     *
     * @param $num
     * @return BcNumber|null
     */
    public static function isNumeric($num): ?BcNumber
    {
        try {
            return new BcNumber($num);
        } catch (\Exception $e) {
        }

        return null;
    }

    /**
     * Checks and accepts Integers, Double/Float values or numeric Strings for BcMath operations
     * @param $num
     * @return string
     */
    public static function Value($num): string
    {
        // Instances of self are obviously valid numbers
        if ($num instanceof BcNumber) {
            return $num->value();
        }

        // Integers are obviously valid numbers
        if (is_int($num)) {
            return strval($num);
        }

        // Floats are valid numbers too but must be checked for scientific E-notations
        if (is_float($num)) {
            $floatAsString = strval($num);
            // Look if scientific E-notation
            if (preg_match('/e\-/i', $floatAsString)) {
                // Auto-detect decimals
                $decimals = preg_split('/e\-/i', $floatAsString);
                $decimals = intval(strlen($decimals[0])) + intval($decimals[1]);
                return number_format($num, $decimals, ".", "");
            } elseif (preg_match('/e\+/i', $floatAsString)) {
                return number_format($num, 0, "", "");
            }

            return $floatAsString;
        }

        // Check with in String
        if (is_string($num)) {
            if (preg_match('/^\-?(0|[1-9]+[0-9]*)(\.[0-9]+)?$/', $num)) {
                return $num;
            }
        }

        throw new \InvalidArgumentException('Passed value cannot be used as number with BcMath lib');
    }
}