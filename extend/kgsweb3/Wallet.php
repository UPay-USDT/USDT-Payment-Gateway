<?php

namespace kgsweb3;

use Elliptic\EC;
use kornrunner\Keccak;
use kgsweb3\RLP;

class Wallet
{
    private $address;
    private $privateKey;

    private function __construct($address, $privateKey)
    {
        $this->address = $address;
        $this->privateKey = $privateKey;
    }

    static public function create(): Wallet
    {
        $ec = new EC('secp256k1');
        $kp = $ec->genKeyPair();
        $privateKey = $kp->getPrivate('hex');
        $pub = $kp->getPublic('hex');
        $address = Utils::pubKeyToAddress($pub);
        return new Wallet($address, $privateKey);
    }


    static public function createByPrivate($privateKey): Wallet
    {
        $ec = new EC('secp256k1');
        // Generate keys
        $key = $ec->keyFromPrivate($privateKey);
        $pub = $key->getPublic('hex');
        // get address based on public key
        return new Wallet(strtolower(Utils::pubKeyToAddress($pub)), $privateKey);
    }


    public function getAddress()
    {
        return $this->address;
    }

    public function getPrivateKey()
    {
        return Utils::add0x($this->privateKey);
    }

    /**
     * @throws \Exception
     */
    public function sign($data)
    {

        if (empty($this->privateKey)) {
            throw new \Exception("please unlock this address");
        }
        $hash = Keccak::hash(hex2bin($data), 256);
        $ec = new EC('secp256k1');
        // Generate keys
        $key = $ec->keyFromPrivate($this->privateKey);
        // Sign message (can be hex sequence or array)
        return $key->sign($hash, ['canonical' => true]);
    }

}