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

use Comely\DataTypes\Buffer\Base16;
use Comely\DataTypes\Buffer\Binary;

/**
 * Class Digest
 * @package Comely\DataTypes\Buffer\Binary
 */
class Digest
{
    /** @var Binary */
    private $buffer;

    /**
     * Digest constructor.
     * @param Binary $binary
     */
    public function __construct(Binary $binary)
    {
        $this->buffer = $binary;
    }

    /**
     * @param string $bytes
     * @return Binary
     */
    private function result(string $bytes): Binary
    {
        return new Binary($bytes);
    }

    /**
     * @param string $algo
     * @param int $iterations
     * @param int $bytes
     * @return Binary
     */
    public function digest(string $algo, int $iterations = 1, int $bytes = 0): Binary
    {
        $hashed = $this->buffer->raw();
        for ($i = 0; $i < $iterations; $i++) {
            $hashed = hash($algo, $hashed, true);
        }

        if ($bytes) {
            $hashed = substr($hashed, 0, $bytes);
        }

        return $this->result($hashed);
    }

    /**
     * @param int $bytes
     * @return Binary
     */
    public function md5(int $bytes = 0): Binary
    {
        return $this->digest("md5", 1, $bytes);
    }

    /**
     * @param int $bytes
     * @return Binary
     */
    public function sha1(int $bytes = 0): Binary
    {
        return $this->digest("sha1", 1, $bytes);
    }

    /**
     * @param int $bytes
     * @return Binary
     */
    public function sha256(int $bytes = 0): Binary
    {
        return $this->digest("sha256", 1, $bytes);
    }

    /**
     * @param int $bytes
     * @return Binary
     */
    public function ripeMd160(int $bytes = 0): Binary
    {
        return $this->digest("ripemd160", 1, $bytes);
    }

    /**
     * @param string $algo
     * @param Binary|string $salt
     * @param int $iterations
     * @param int $length
     * @return Binary
     */
    public function pbkdf2(string $algo, $salt, int $iterations, int $length = 0): Binary
    {
        if ($salt instanceof Binary) {
            $salt = $salt->raw();
        }

        if (!is_string($salt)) {
            throw new \InvalidArgumentException('Invalid value for PBKDF2 param "salt"');
        }

        return $this->result(hash_pbkdf2($algo, $this->buffer->raw(), $salt, $iterations, $length, true));
    }

    /**
     * @param string $algo
     * @param $key
     * @return Binary
     */
    public function hmac(string $algo, $key): Binary
    {
        if ($key instanceof Base16) {
            $key = $key->binary()->raw();
        } elseif ($key instanceof Binary) {
            $key = $key->raw();
        }

        if (!is_string($key)) {
            throw new \InvalidArgumentException('Invalid value for HMAC param "key"');
        }

        return $this->result(hash_hmac($algo, $this->buffer->raw(), $key, true));
    }
}