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

namespace Comely\DataTypes\Buffer\Binary;

use Comely\DataTypes\Buffer\Binary;

/**
 * Class ByteReader
 * @package Comely\DataTypes\Buffer\Binary
 */
class ByteReader
{
    public const UNDERFLOW_EX_SIGNAL = 0x1f40;

    /** @var string */
    private $buffer;
    /** @var int */
    private $pointer;
    /** @var int */
    private $len;
    /** @var bool */
    private $throwUnderflowEx;

    /**
     * ByteReader constructor.
     * @param Binary $binary
     */
    public function __construct(Binary $binary)
    {
        $this->buffer = $binary->raw() ?? "";
        $this->len = strlen($this->buffer);
        $this->pointer = 0;
        $this->throwUnderflowEx = false;
    }

    /**
     * @return bool
     */
    public function isEnd(): bool
    {
        return $this->pointer >= $this->len ? true : false;
    }

    /**
     * @return $this
     */
    public function throwUnderflowEx(): self
    {
        $this->throwUnderflowEx = true;
        return $this;
    }

    /**
     * @return int
     */
    public function len(): int
    {
        return $this->len;
    }

    /**
     * @return int
     */
    public function pos(): int
    {
        return $this->pointer;
    }

    /**
     * Start reading from beginning
     * @return ByteReader
     */
    public function reset(): self
    {
        $this->pointer = 0;
        return $this;
    }

    /**
     * Reset pointer, get next N bytes from top
     * @param int $bytes
     * @return string|null
     */
    public function first(int $bytes): ?string
    {
        return $this->reset()->next($bytes);
    }

    /**
     * Return next N bytes
     * @param int $bytes
     * @return string|null
     */
    public function next(int $bytes): ?string
    {
        if ($this->throwUnderflowEx) {
            if (($this->pointer + $bytes) > $this->len) {
                throw new \UnderflowException(
                    sprintf('Attempt to read next %d bytes, while only %d available', $bytes, ($this->len - $this->pointer)),
                    self::UNDERFLOW_EX_SIGNAL
                );
            }
        }

        $read = substr($this->buffer, $this->pointer, $bytes);
        if ($read) {
            if (strlen($read) === $bytes) {
                $this->pointer += $bytes;
                return $read;
            }
        }

        if ($this->throwUnderflowEx) {
            throw new \UnderflowException(
                sprintf('ByteReader ran out of bytes at pos %d', $this->pointer),
                self::UNDERFLOW_EX_SIGNAL
            );
        }

        return null;
    }

    /**
     * @param int $pos
     * @return $this
     */
    public function setPointer(int $pos): self
    {
        if ($pos < 0 || $pos > $this->len) {
            throw new \RangeException('Invalid pointer position or is out of range');
        }

        $this->pointer = $pos;
        return $this;
    }

    /**
     * @return string|null
     */
    public function remaining(): ?string
    {
        $remaining = substr($this->buffer, $this->pointer);
        return $remaining ? $remaining : null;
    }
}
