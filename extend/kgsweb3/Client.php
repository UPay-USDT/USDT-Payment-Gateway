<?php

namespace kgsweb3;

use GuzzleHttp\Client as GuzzleHttp;

use Elliptic\EC;
use kornrunner\Keccak;

class Client
{
    // instence
    public static $instence = null;

    // client
    public $client = null;

    // requestId
    protected $requestId = 0;

    // address => key
    protected $addrpri = [];

    // chainId
    protected $chainId = null;

    // other path
    public $path = '';

    /**
     * @param @options
     */
    public function __construct($options = [])
    {
        $defaultOptions = [
            'base_uri' => 'http://127.0.0.1:8545',
            'timeout' => 10,
            'verify' => false,
        ];
        if (is_string($options)) {
            $this->client = new GuzzleHttp(array_merge($defaultOptions, ['base_uri' => $options]));
        } else {
            $this->client = new GuzzleHttp(array_merge($defaultOptions, $options));
        }
    }

    /**
     * 设置私钥
     */
    public function addPrivateKeys(array $privs)
    {
        $temp = [];
        $ec = new EC('secp256k1');
        foreach ($privs as $key => $value) {
            // Generate keys
            $key = $ec->keyFromPrivate($value);
            $pub = $key->getPublic('hex');
            // 根据公钥获取地址
            $addr = strtolower(Utils::pubKeyToAddress($pub));
            $temp[$addr] = $value;
        }
        $this->addrpri = array_unique(array_merge($this->addrpri, $temp));
    }

    /**
     * 移除私钥
     */
    public function removePrivateKeys(array $privs)
    {
        $temp = [];
        $ec = new EC('secp256k1');
        foreach ($privs as $key => $value) {
            // Generate keys
            $key = $ec->keyFromPrivate($value);
            $pub = $key->getPublic('hex');
            // 根据公钥获取地址
            $addr = strtolower(Utils::pubKeyToAddress($pub));
            $temp[$addr] = $value;
        }
        $this->addrpri = array_unique(array_diff($this->addrpri, $temp));
    }

    /**
     * 通过地址拿到私钥
     */
    public function getPrivateByAddress($addr)
    {
        if (isset($this->addrpri[strtolower($addr)])) {
            return $this->addrpri[strtolower($addr)];
        }
        throw new \Exception("NO private from {$addr}", 1);
    }

    /**
     * 离线签署并发出交易
     */
    public function sendTransaction(array $transaction)
    {
        if (!isset($transaction['from'])) {
            throw new \Exception('The transaction format is error.', 1);
        }
        $addr = $transaction['from'];
        // 去掉from
        unset($transaction['from']);
        // 获得networkId/chainId
        $chainId = $this->getChainId();
        // 合并数据
        $transaction = array_merge([
            'nonce' => '01',
            'gasPrice' => '',
            'gas' => '',
            'to' => '',
            'value' => '',
            'data' => '',
        ], $transaction);
        
        // EIP-155
        if ($chainId > 0) {
            $transaction['v']=dechex($chainId);
            $transaction['r']='';
            $transaction['s']='';
        }

        // serialize
        $raw = $this->rawEncode($transaction);
        // sign
        $signature = $this->sign($addr, $raw);
        // 按照这个顺序，不然序列会错误
        $transaction['v'] = dechex($signature->recoveryParam + 27 + ($chainId ? $chainId * 2 + 8 : 0));
        $transaction['r'] = $signature->r->toString('hex');
        $transaction['s'] = $signature->s->toString('hex');
        // 签署的RAW
        $signRaw = $this->rawEncode($transaction);

        // 发送交易
        return $this->eth_sendRawTransaction(Utils::add0x($signRaw));
    }

    /**
     * 对交易数据进行签名
     * @param $pri 十六进制私钥
     * @param $msg 十六进制数据
     * @return $signature
     */
    public function sign($addr, $data)
    {
        // 得到私钥
        $prikey = $this->getPrivateByAddress($addr);
        // sha1
        $hash = Keccak::hash(hex2bin($data), 256);

        $ec = new EC('secp256k1');
        // Generate keys
        $key = $ec->keyFromPrivate($prikey);
        // Sign message (can be hex sequence or array)
        $signature = $key->sign($hash, ['canonical' => true]);

        // Verify signature
        return $signature;
    }

    /**
     * RLPencode
     */
    public function rawEncode(array $input): string
    {
        $rlp  = new \kgsweb3\RLP;
        $data = [];
        foreach ($input as $item) {
            // 如果值是无效值：0、0x0，将其列为空串
            $data[] = $item && hexdec(Utils::remove0x($item)) != 0 ? Utils::add0x($item) : '';
        }
        return $rlp->encode($data)->toString('hex');
    }

    /**
     * 新建账户
     * @return 私钥和地址
     */
    public function newAccount()
    {
        $ec = new EC('secp256k1');
        $kp = $ec->genKeyPair();
        $pri = $kp->getPrivate('hex');
        $pub = $kp->getPublic('hex');
        // 根据公钥生成地址
        $addr = Utils::pubKeyToAddress($pub);
        return [$addr, $pri];
    }

    /**
     * 调用
     */
    public function __call($method, $args = [])
    {
        $default = $this->getDefaultArgs($method);
        // 填充参数默认值
        foreach ($default as $key => $value) {
            if (!isset($args[$key])) {
                $args[$key] = $value;
            }
        }
        return $this->request($method, $args);
    }

    /**
     * 获取默认的参数
     */
    public function getDefaultArgs($method)
    {
        switch ($method) {
            case 'eth_getBalance':
                return [0 => '', 1 => 'latest'];
                break;
            // TODO::more
            default:
                // code...
                break;
        }
        return [];
    }

    /**
     * 获取ChainId
     */
    public function getChainId()
    {
        if ($this->chainId === null) {
            $this->chainId = $this->net_version();
        }
        return $this->chainId;
    }

    /**
     * 发出请求
     */
    public function request($method, $params = [])
    {
        $data = [
            'json' => [
                'jsonrpc'=> '2.0',
                'method' => $method,
                'params' => $params,
                'id' => $this->requestId++,
            ],
        ];
        $res = $this->client->post($this->path, $data);
        $body = json_decode($res->getBody());
        if (isset($body->error) && !empty($body->error)) {
            throw new \Exception($body->error->message . " [Method] {$method}", $body->error->code);
        }
        return $body->result;
    }
}
