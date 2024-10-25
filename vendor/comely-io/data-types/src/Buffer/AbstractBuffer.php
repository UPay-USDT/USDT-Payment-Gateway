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

namespace Comely\DataTypes\Buffer;

/**
 * Class AbstractBuffer
 * @package Comely\DataTypes\Buffer
 * @property-read int $sizeInBytes
 * @property-read int $length
 */
abstract class AbstractBuffer implements \Serializable
{
    /** @var string */
    private $data;
    /** @var int Length of buffered data, this is char count and NOT in bytes */
    private $len;
    /** @var int Size of buffered data in bytes */
    private $size;
    /** @var bool */
    private $readOnly;

    /**
     * AbstractBuffer constructor.
     * @param string|null $data
     */
    public function __construct(?string $data = null)
    {
        $this->readOnly = false;
        $this->data = "";
        $this->len = 0;
        $this->size = 0;

        $this->set($data);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            "len" => $this->len,
            "size" => $this->size
        ];
    }

    /**
     * @param $prop
     * @return int
     */
    public function __get($prop)
    {
        switch ($prop) {
            case "sizeInBytes":
                return $this->size;
            case "length":
                return $this->len;
        }

        throw new \OutOfBoundsException('Cannot get value of inaccessible property');
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        // Read only?
        return sprintf(
            '%d:%d:%s',
            $this->readOnly === true ? 1 : 0,
            $this->size,
            base64_encode($this->data)
        );
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        // int readOnly + ":" + strlen( data ) + ":" + base64(data)
        if (!is_string($serialized) || !preg_match('/^[0-1]:[0-9]+:[a-z0-9+\/]+={0,2}$/i', $serialized)) {
            throw new \InvalidArgumentException('Serialized data mismatch');
        }

        $splits = explode(":", $serialized);
        $dataSize = intval($splits[1]);

        // Construct object
        $this->readOnly = false;
        $this->data = "";
        $this->len = 0;
        $this->size = 0;

        // Restore data
        $this->set(base64_decode($splits[2]));
        if ($this->size !== $dataSize) {
            throw new \UnexpectedValueException('Serialized data size does not match');
        }

        // ReadOnly flag
        $this->readOnly = intval($splits[0]) === "1";
    }

    /**
     * @param string $data
     * @return string
     */
    abstract protected function validatedDataTypeValue(?string $data): string;

    /**
     * @param string $validatedData
     */
    private function setBufferData(string $validatedData): void
    {
        if ($this->readOnly) {
            throw new \BadMethodCallException('Buffer is in read-only state');
        }

        $this->data = $validatedData;
        $this->size = strlen($this->data);
        $this->len = mb_strlen($this->data);
    }

    /**
     * @param bool $set
     * @return $this
     */
    public function readOnly(bool $set = true)
    {
        $this->readOnly = $set;
        return $this;
    }

    /**
     * @param string|null $data
     * @return $this
     */
    public function set(?string $data = null)
    {
        if($data) {
            $this->setBufferData($this->validatedDataTypeValue($data));
        }

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
     * @param $data
     * @return $this
     */
    public function append($data)
    {
        if ($data instanceof AbstractBuffer) {
            $data = $data->value();
        }

        if (!is_string($data)) {
            throw new \InvalidArgumentException('Appending data must be of type String or a Buffer');
        }

        $validated = $this->validatedDataTypeValue($data);
        $this->setBufferData($this->data . $validated);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function prepend($data)
    {
        if ($data instanceof AbstractBuffer) {
            $data = $data->value();
        }

        if (!is_string($data)) {
            throw new \InvalidArgumentException('Prepend data must be of type String or a Buffer');
        }

        $validated = $this->validatedDataTypeValue($data);
        $this->setBufferData($validated . $this->data);
        return $this;
    }

    /**
     * @param int|null $start
     * @param int|null $length
     * @return string|null
     */
    public function value(?int $start = null, ?int $length = null): ?string
    {
        if (!is_string($this->data)) {
            return null;
        }

        $data = $this->data;
        if (is_int($start)) {
            $data = is_int($length) ? substr($data, $start, $length) : substr($data, $start);
            if ($data === false) {
                return null;
            }
        }

        return $data;
    }

    /**
     * @param int|null $start
     * @param int|null $end
     * @return static
     */
    public function copy(?int $start = null, ?int $end = null)
    {
        return new static($this->value($start, $end) ?? "");
    }

    /**
     * @return static
     */
    public function clone()
    {
        return $this->copy();
    }

    /**
     * @return static
     */
    public function __clone()
    {
        return $this->clone();
    }

    /**
     * @param int|null $start
     * @param int|null $length
     * @return $this
     */
    public function substr(?int $start = null, ?int $length = null)
    {
        if (!$start && !$length) {
            throw new \InvalidArgumentException('Both start/end arguments cannot be empty');
        }

        $data = $this->data;
        if (is_int($start)) {
            $data = is_int($length) ? substr($data, $start, $length) : substr($data, $start);
            if ($data === false) {
                throw new \UnexpectedValueException('Unexpected fail after applying substr');
            }
        }

        $this->set($data);
        return $this;
    }

    /**
     * Compare 2 Buffers, Returns true if they are of same type, size and buffered data matches
     * @param AbstractBuffer $buffer
     * @return bool
     */
    public function equals(AbstractBuffer $buffer): bool
    {
        if (get_class($this) === get_class($buffer)) {
            if ($this->size === $buffer->sizeInBytes) {
                if ($this->value() === $buffer->value()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Closure $callback
     */
    public function apply(\Closure $callback): void
    {
        $new = $callback($this->data);
        if (!is_string($new)) {
            throw new \UnexpectedValueException('Callback method supplied to "apply" method must return String');
        }

        $this->set($new);
    }
}
