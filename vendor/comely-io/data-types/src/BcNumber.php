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

namespace Comely\DataTypes;

use Comely\DataTypes\BcMath\BaseConvert;
use Comely\DataTypes\BcMath\BcMath;
use Comely\DataTypes\Buffer\Base16;
use Comely\DataTypes\Buffer\Bitwise;

/**
 * Class BcNumber
 * @package Comely\DataTypes
 */
class BcNumber
{
    /** @var null|int */
    private $scale;
    /** @var string */
    private $original;
    /** @var string */
    private $value;
    /** @var null|bool */
    private $triggerUpdateSelf;

    /**
     * @param Base16 $hexits
     * @return BcNumber
     */
    public static function fromBase16(Base16 $hexits): self
    {
        return new self(BcMath::Decode($hexits->hexits(false)));
    }

    /**
     * BcNumber constructor.
     * @param null $num
     */
    public function __construct($num = null)
    {
        $this->original = $num ? $this->checkValidNum($num) : "0";
        $this->value = $this->original;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            "original" => $this->original,
            "value" => $this->value,
            "scale" => $this->getScale()
        ];
    }

    /**
     * @param int $scale
     * @return BcNumber
     */
    public function scale(int $scale): self
    {
        if ($scale < 0) {
            throw new \InvalidArgumentException('BcMath scale value must be a positive integer');
        }

        $this->scale = $scale;
        return $this;
    }

    /**
     * @param int $retain
     * @return BcNumber
     */
    public function trim(int $retain = 0): self
    {
        if ($this->isInteger()) {
            return $this;
        }

        $trimmed = rtrim(rtrim($this->value, "0"), ".");
        if ($retain) {
            $trimmed = explode(".", $trimmed);
            $decimals = $trimmed[1] ?? "";
            $required = $retain - strlen($decimals);
            if ($required > 0) {
                $trimmed = $trimmed[0] . "." . $decimals . str_repeat("0", $required);
            }
        }

        $this->value = $trimmed;
        return $this;
    }

    /**
     * Returns original number (given on constructor)
     * @return string
     */
    public function original(): string
    {
        return $this->original;
    }

    /**
     * Gets value as string
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function int(): int
    {
        if (!$this->isInteger()) {
            throw new \DomainException('Stored BcNumber value is not integer');
        }

        if (bccomp(strval(PHP_INT_MAX), $this->value, 0) === 1) {
            throw new \DomainException('Stored BcNumber cannot be converted to signed PHP integer, exceeds PHP_INT_MAX');
        }

        return intval($this->value);
    }

    /**
     * Checks if value is integral (does not have decimals)
     * @return bool
     */
    public function isInteger(): bool
    {
        return preg_match('/^-?(0|[1-9]+[0-9]*)$/', $this->value) ? true : false;
    }

    /**
     * Compare number with another
     * @param $comp
     * @param int|null $scale
     * @return int
     */
    public function cmp($comp, ?int $scale = null): int
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale));
    }

    /**
     * Checks if value is zero
     * @return bool
     */
    public function isZero(): bool
    {
        return bccomp($this->value, "0", $this->getScale()) === 0 ? true : false;
    }

    /**
     * Checks if value is greater than zero
     * @return bool
     */
    public function isPositive(): bool
    {
        return bccomp($this->value, "0", $this->getScale()) === 1 ? true : false;
    }

    /**
     * Checks if value is less than zero
     * @return bool
     */
    public function isNegative(): bool
    {
        return bccomp($this->value, "0", $this->getScale()) === -1 ? true : false;
    }

    /**
     * Compares value with a number to check if both are equal
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function equals($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === 0 ? true : false;
    }

    /**
     * Compares value with a number to check if value is greater than argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function greaterThan($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === 1 ? true : false;
    }

    /**
     * Compares value with a number to check if value is greater than or equals argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function greaterThanOrEquals($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === -1 ? false : true;
    }

    /**
     * Compares value with a number to check if value is less than argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function lessThan($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === -1 ? true : false;
    }

    /**
     * Compares value with a number to check if value is less than or equals argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function lessThanOrEquals($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === 1 ? false : true;
    }

    /**
     * Checks if value is within (or equals) given min and max arguments
     * @param $min
     * @param $max
     * @param int|null $scale
     * @return bool
     */
    public function inRange($min, $max, ?int $scale = null): bool
    {
        $min = $this->checkValidNum($min);
        $max = $this->checkValidNum($max);

        $scale = $this->getScale($scale);
        if (bccomp($this->value, $min, $scale) !== -1) {
            if (bccomp($this->value, $max, $scale) !== 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function add($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        return $this->result(bcadd($this->value, $num, $this->getScale($scale)));
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function sub($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        return $this->result(bcsub($this->value, $num, $this->getScale($scale)));
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function mul($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        return $this->result(bcmul($this->value, $num, $this->getScale($scale)));
    }

    /**
     * @param int $base
     * @param int $exponent
     * @param int|null $scale
     * @return BcNumber
     */
    public function mulPow(int $base, int $exponent, ?int $scale = null): self
    {
        if ($base < 1) {
            throw new \InvalidArgumentException('Value for param "base" must be a positive integer');
        } elseif ($exponent < 1) {
            throw new \InvalidArgumentException('Value for param "exponent" must be a positive integer');
        }

        return $this->result(bcmul(
            $this->value,
            bcpow(strval($base), strval($exponent), 0),
            $this->getScale($scale)
        ));
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function divide($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        return $this->result(bcdiv($this->value, $num, $this->getScale($scale)));
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function pow($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        return $this->result(bcpow($this->value, $num, $this->getScale($scale)));
    }

    /**
     * @param $divisor
     * @param int|null $scale
     * @return BcNumber
     */
    public function mod($divisor, ?int $scale = null): self
    {
        $num = $this->checkValidNum($divisor);
        return $this->result(bcmod($this->value, $num, $this->getScale($scale)));
    }

    /**
     * @param $divisor
     * @param int|null $scale
     * @return BcNumber
     */
    public function remainder($divisor, ?int $scale = null): self
    {
        return $this->mod($divisor, $scale);
    }

    /**
     * @return BcNumber
     */
    public function copy(): self
    {
        return (new self($this->value))
            ->scale($this->getScale());
    }

    /**
     * Next 1 calculation will update value of same instance, result will NOT create new instance of BigNumber
     * @return BcNumber
     */
    public function update(): self
    {
        $this->triggerUpdateSelf = true;
        return $this;
    }

    /**
     * @return Base16
     */
    public function encode(): Base16
    {
        return $this->toBase16();
    }

    /**
     * @return Base16
     */
    public function toBase16(): Base16
    {
        return new Base16(BaseConvert::fromBase10($this, BaseConvert::CHARSET_BASE16));
    }

    /**
     * @return Bitwise
     */
    public function toBitwise(): Bitwise
    {
        return new Bitwise(BaseConvert::fromBase10($this, BaseConvert::CHARSET_BINARY));
    }

    /**
     * @param string $new
     * @return BcNumber
     */
    private function result(string $new): self
    {
        if ($this->triggerUpdateSelf === true) {
            $this->value = $new;
            $this->triggerUpdateSelf = false;
            return $this;
        }

        return (new self($new))
            ->scale($this->getScale());
    }

    /**
     * @param int|null $scale
     * @return int
     */
    private function getScale(?int $scale = null): int
    {
        if (is_int($scale) && $scale > 0) {
            return $scale;
        }

        if (is_int($this->scale)) {
            return $this->scale;
        }

        return 0;
    }

    /**
     * Checks and accepts Integers, Double/Float values or numeric Strings for BcMath operations
     * @param $num
     * @return string
     */
    private function checkValidNum($num): string
    {
        return BcMath::Value($num);
    }
}