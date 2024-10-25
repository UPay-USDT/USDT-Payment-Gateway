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

use Comely\DataTypes\BcMath\BcMath;
use Comely\DataTypes\DataTypes;

/**
 * Class Bitwise
 * @package Comely\DataTypes\Buffer
 */
class Bitwise extends AbstractBuffer
{
    /**
     * @param string|null $data
     * @return string
     */
    public function validatedDataTypeValue(?string $data): string
    {
        if (!DataTypes::isBitwise($data)) {
            throw new \InvalidArgumentException('First argument must be a Binary bitwise (1s and 0s) value');
        }

        return $data;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            "data" => $this->value() ?? "",
            "len" => $this->len()
        ];
    }

    /**
     * @return Base16
     */
    public function base16(): Base16
    {
        return new Base16(BcMath::BaseConvert($this->value() ?? "", 2, 16));
    }

    /**
     * @return Binary
     */
    public function binary(): Binary
    {
        return $this->base16()->binary();
    }

    /**
     * @return array
     */
    public function bytes(): array
    {
        return $this->chunks(8);
    }

    /**
     * @param int $len
     * @return array
     */
    public function chunks(int $len): array
    {
        return explode(" ", chunk_split($this->value() ?? "", $len, " "));
    }
}