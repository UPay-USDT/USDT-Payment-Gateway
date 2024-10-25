<?php

namespace kgsweb3;

use kornrunner\Keccak;

class Contract
{
    private $address;
    private $abi;
    private $web3;
    private $event;
    private $eventHash;
    private $function;

    private function __construct(Web3 $web3, $abi, $address)
    {
        $this->abi = $abi;
        $this->web3 = $web3;
        $this->address = $address;

        $json = json_decode($this->abi, true);

        foreach ($json as $item) {
            switch ($item['type']) {
                case 'event':
                    $this->event[$item['name']] = $item;
                    $hash = $this->encodeEventSignature($this->getEventByName($item['name']));
                    $this->eventHash[$hash] = $item['name'];
                    break;
                case 'function':
                    $this->function[$item['name']] = $item;
                    break;
            }
        }
    }

    public static function at(Web3 $web3, $abi, $address): Contract
    {
        return new Contract($web3, $abi, $address);
    }

    public function getFunctionArrayByName($name)
    {
        return $this->function[$name];
    }

    public function getEventArrayByName($name)
    {
        return $this->event[$name];
    }

    public function getFunctionByName($name): string
    {
        $inputs = $this->function[$name]['inputs'];
        return $this->extracted($name, $inputs);
    }

    public function getEventByName($name): string
    {
        $inputs = $this->event[$name]['inputs'];
        return $this->extracted($name, $inputs);
    }

    public function getFunctionName(): array
    {
        return array_keys($this->function);
    }

    public function getEventName(): array
    {
        return array_keys($this->event);
    }
    public function decodeEvent($hash){
        if(!array_key_exists($hash,$this->eventHash)){
          throw  new \Exception("this hash not in the constract");
        }
        return $this->eventHash[$hash];
    }
    public function getAbi()
    {
        return $this->abi;
    }

    /**
     * @throws \Exception
     */
    public function send(Wallet $wallet, $function, array $param, $config = [])
    {

        $data = $this->getData($function, $param, $wallet->getAddress());
        $data['gas'] = dechex(hexdec(
                $this->web3->estimateGas($data['to'], $data['data'], $data['from'], null, $data['value']
                )) * 1.5);
        $data['gasPrice'] = $this->web3->gasPrice();
        $data['nonce'] = $this->web3->getTransactionCount($wallet->getAddress(), 'pending');
        unset($data['from']);
//        $chainId = $this->web3->chainId();
        $data = array_merge([
            'nonce' => '01',
            'gasPrice' => '',
            'gas' => '',
            'to' => '',
            'value' => '',
            'data' => '',
        ], $data);
        $signature = $wallet->sign(Utils::rawEncode($data));
        $chainId = 0;
        $data['v'] = dechex($signature->recoveryParam + 27 + ($chainId ? $chainId * 2 + 8 : 0));
        $data['r'] = $signature->r->toString('hex');
        $data['s'] = $signature->s->toString('hex');
        $signRaw = Utils::add0x(Utils::rawEncode($data));
        return $this->web3->sendRawTransaction($signRaw);
    }

    /**
     * @param $function
     * @param $param
     * @param null $from
     * @return array
     * @throws \Exception
     */
    private function getData($function, $param, $from = null): array
    {
        if (!array_key_exists($function, $this->function)) {
            throw new \Exception(" function not in contract ");
        }
        $function = $this->getFunctionArrayByName($function);
        if (count($param) != count($function['inputs'])) {
            throw  new  \Exception("please send full param");
        }
        $data = [
            'to' => $this->address,
            'value' => '0x0'
        ];
        if (!empty($from)) {
            $data['from'] = $from;
        }
        $hash = Keccak::hash($this->getFunctionByName($function['name']), 256);
        $hashSub = mb_substr($hash, 0, 8, 'utf-8');
        $data['data'] = '0x' . $hashSub;
        $input = $function['inputs'];
        for ($i = 0; $i < count($param); $i++) {
            $value = '';
            switch ($input[$i]['type']) {
                case 'address':
                    $value = Utils::remove0x($param[$i]);
                    break;
                case 'uint8':
                case 'uint16':
                case 'uint24':
                case 'uint32':
                case 'uint40':
                case 'uint48':
                case 'uint56':
                case 'uint64':
                case 'uint72':
                case 'uint80':
                case 'uint88':
                case 'uint96':
                case 'uint104':
                case 'uint112':
                case 'uint120':
                case 'uint128':
                case 'uint136':
                case 'uint144':
                case 'uint152':
                case 'uint160':
                case 'uint168':
                case 'uint176':
                case 'uint184':
                case 'uint192':
                case 'uint200':
                case 'uint208':
                case 'uint216':
                case 'uint224':
                case 'uint232':
                case 'uint240':
                case 'uint248':
                case 'uint256':
                    $value = Utils::decToHex($param[$i], false);
                    break;
            }
            $data['data'] = $data['data'] . Utils::fill0($value);
        }
        return $data;
    }

    /**
     * encodeEventSignature
     * TODO: Fix same event name with different params
     *
     * @param string|stdClass|array $functionName
     * @return string
     */
    public function encodeEventSignature($functionName)
    {
        if (!is_string($functionName)) {
            $functionName = Utils::jsonMethodToString($functionName);
        }
        return Utils::sha3($functionName);
    }

    public function call($function, $param = [],$quantity=Quantity::latest)
    {
        $data = $this->getData($function, $param);
        return $this->web3->call($data['to'], $data['data'],null,null,null,null,$quantity);
    }


    /**
     * @param $name
     * @param $inputs
     * @return string
     */
    public function extracted($name, $inputs): string
    {
        $res = $name . '(';
        foreach ($inputs as $input) {
            $res .= $input['type'] . ',';
        }
        if (count($inputs) > 0) {
            $res = substr($res, 0, strlen($res) - 1);
        }
        $res .= ')';
        return $res;
    }
}