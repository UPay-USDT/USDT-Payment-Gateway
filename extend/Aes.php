<?php
//AES加解密工具
class Aes {
     /**
     * @var string 秘钥
     * AES-128-CBC key 长度 16 位,IV 16位
     * AES-192-CBC key 长度 24 位,IV 16位
     * AES-256-CBC key 长度 32 位,IV 16位
     */
    protected $securityKey;

    /**
     * @var string 加密方式 https://www.php.net/manual/zh/function.openssl-get-cipher-methods.php
     */
    protected $method;

    /**
     * @var string 偏移量
     */
    protected $iv;

    /**
     * Aes constructor.
     * @param string $securityKey
     * @param string $method
     * @param string $iv
     */
    public function __construct(string $securityKey, string $method = 'AES-128-CBC', string $iv = '')
    {
        if (empty($securityKey)) {
            throw new \RuntimeException('秘钥不能为空');
        }
        $this->securityKey = $securityKey;
        if (false === $this->isSupportCipherMethod($method)) {
            throw new \RuntimeException('暂不支持该加密方式');
        }
        $this->method = $method;

        $this->iv = $this->initializationVector($method, $iv);
    }


    /**
     * 加密
     * @param string $plainText 明文
     * @return bool|string
     */
    public function encrypt(string $plainText)
    {
        $originData = (openssl_encrypt($this->addPkcs7Padding($plainText, 16), $this->method, $this->securityKey, OPENSSL_NO_PADDING, $this->iv));
        return $originData === false ? false : base64_encode($originData);
    }

    /**
     * 解密
     * @param string $cipherText 密文
     * @return bool|string
     */
    public function decrypt(string $cipherText)
    {
        $str = base64_decode($cipherText);
        $data = openssl_decrypt($str, $this->method, $this->securityKey, OPENSSL_NO_PADDING, $this->iv);
        return $data === false ? false : $this->stripPKSC7Padding($data);
    }

    /**
     * 初始化向量
     * @param string $method
     * @param string $iv
     * @return false|string
     */
    private function initializationVector(string $method, string $iv = '')
    {
        $originIvLen = openssl_cipher_iv_length($method);
        if(false === $originIvLen) { return ''; }
        $currentIvLen = strlen($iv);
        if ($originIvLen === $currentIvLen) {
            $outIv = $iv;
        } elseif ($currentIvLen < $originIvLen) {
            $outIv = $iv . str_repeat("\0", $originIvLen - $currentIvLen);
        } elseif ($currentIvLen > $originIvLen) {
            $outIv = substr($iv, 0, $originIvLen);
        } else {
            $outIv = str_repeat("\0", $originIvLen);
        }
        return $outIv;
    }

    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    private function addPKCS7Padding($source): string
    {
        $source = trim($source);
        $block = 16;

        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }

    /**
     * 是否支持该加密方式
     * @param string $method
     * @return bool
     */
    private function isSupportCipherMethod(string $method): bool
    {
        $method = strtoupper($method);
        if (in_array($method, openssl_get_cipher_methods(), true)) {
            return true;
        }
        return false;
    }

    /**
     * 移去填充算法
     * @param string $source
     * @return string
     */
    private function stripPKSC7Padding($source): string
    {
        $char = substr($source, -1);
        $num = ord($char);
        if ($num === 62) return $source;
        $source = substr($source, 0, -$num);
        return $source;
    }

    /**
     * 十六进制转字符串
     * @param $hex
     * @return string
     */
    private function hexToStr($hex): string
    {
        $string = "";
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    /**
     * 字符串转十六进制
     * @param $string
     * @return string
     */
    private function strToHex($string): string
    {
        $hex = "";
        $tmp = "";
        $iMax = strlen($string);
        for ($i = 0; $i < $iMax; $i++) {
            $tmp = dechex(ord($string[$i]));
            $hex .= strlen($tmp) === 1 ? "0" . $tmp : $tmp;
        }
        $hex = strtoupper($hex);
        return $hex;
    }
}

