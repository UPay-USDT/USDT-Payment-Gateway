<?php

declare(strict_types=1);

namespace IEXBase\TronAPI\Support;

use kornrunner\Secp256k1;
use kornrunner\Signature\Signature;

class Secp
{
    public static function sign(string $message, string $privateKey, bool $bool = false): string
    {
        $secp = new Secp256k1();
        
        /** @var Signature $sign */
        $sign = $secp->sign($message, $privateKey, ['canonical' => $bool]);

        return $sign->toHex() . bin2hex(implode('', array_map('chr', [$sign->getRecoveryParam()])));
    }
}
