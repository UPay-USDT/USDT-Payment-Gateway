<?php
// 应用公共文件

use app\common\service\AuthService;
use think\facade\Cache;
use IEXBase\TronAPI\Tron;
use kornrunner\Keccak;
use think\facade\Db;
use think\facade\Log;

if (!function_exists('__url')) {

    /**
     * 构建URL地址
     * @param string $url
     * @param array $vars
     * @param bool $suffix
     * @param bool $domain
     * @return string
     */
    function __url(string $url = '', array $vars = [], $suffix = true, $domain = false)
    {
        return url($url, $vars, $suffix, $domain)->build();
    }
}

if (!function_exists('password')) {

    /**
     * 密码加密算法
     * @param $value 需要加密的值
     * @param $type  加密类型，默认为md5 （md5, hash）
     * @return mixed
     */
    function password($value)
    {
        $value = sha1('blog_') . md5($value) . md5('_encrypt') . sha1($value);
        return sha1($value);
    }

}

if (!function_exists('xdebug')) {

    /**
     * debug调试
     * @param string|array $data 打印信息
     * @param string $type 类型
     * @param string $suffix 文件后缀名
     * @param bool $force
     * @param null $file
     */
    function xdebug($data, $type = 'xdebug', $suffix = null, $force = false, $file = null)
    {
        !is_dir(runtime_path() . 'xdebug/') && mkdir(runtime_path() . 'xdebug/');
        if (is_null($file)) {
            $file = is_null($suffix) ? runtime_path() . 'xdebug/' . date('Ymd') . '.txt' : runtime_path() . 'xdebug/' . date('Ymd') . "_{$suffix}" . '.txt';
        }
        file_put_contents($file, "[" . date('Y-m-d H:i:s') . "] " . "========================= {$type} ===========================" . PHP_EOL, FILE_APPEND);
        $str = ((is_string($data) ? $data : (is_array($data) || is_object($data))) ? print_r($data, true) : var_export($data, true)) . PHP_EOL;
        $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }
}

if (!function_exists('sysconfig')) {

    /**
     * 获取系统配置信息
     * @param $group
     * @param null $name
     * @return array|mixed
     */
    function sysconfig($group, $name = null)
    {
        $where = ['group' => $group];
        $value = empty($name) ? Cache::get("sysconfig_{$group}") : Cache::get("sysconfig_{$group}_{$name}");
        if (empty($value)) {
            if (!empty($name)) {
                $where['name'] = $name;
                $value = \app\admin\model\SystemConfig::where($where)->value('value');
                Cache::tag('sysconfig')->set("sysconfig_{$group}_{$name}", $value, 180);
            } else {
                $value = \app\admin\model\SystemConfig::where($where)->column('value', 'name');
                Cache::tag('sysconfig')->set("sysconfig_{$group}", $value, 180);
            }
        }
        return $value;
    }
}

if (!function_exists('sysconfignocache')) {

    /**
     * 获取系统配置信息
     * @param $group
     * @param null $name
     * @return array|mixed
     */
    function sysconfignocache($group, $name = null)
    {
        $where = ['group' => $group];
        $where['name'] = $name;
        $value = \app\admin\model\SystemConfig::where($where)->value('value');
        return $value;
    }
}

if (!function_exists('gettuser')) {

    /**
     * 获取系统配置信息
     * @param $group
     * @param null $name
     * @return array|mixed
     */
    function gettuser()
    {
        $where['appid'] = 'ciofh5fe';
        $value = \app\common\model\MerchantMerchant::where($where)->value('id');
        if (!empty($value)) {
            return $value;
        }
        return -1;
    }
}

if (!function_exists('array_format_key')) {

    /**
     * 二位数组重新组合数据
     * @param $array
     * @param $key
     * @return array
     */
    function array_format_key($array, $key)
    {
        $newArray = [];
        foreach ($array as $vo) {
            $newArray[$vo[$key]] = $vo;
        }
        return $newArray;
    }

}

if (!function_exists('auth')) {

    /**
     * auth权限验证
     * @param $node
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function auth($node = null)
    {
        $authService = new AuthService(session('admin.id'));
        $check = $authService->checkNode($node);
        return $check;
    }

}

if (!function_exists('check_cors_request')) {
    /**
     * 跨域检测
     */
    function check_cors_request()
    {
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
            $info = parse_url($_SERVER['HTTP_ORIGIN']);
            $domainArr = explode(',', config('fastadmin.cors_request_domain'));
            $domainArr[] = request()->host(true);
            if (in_array("*", $domainArr) || in_array($_SERVER['HTTP_ORIGIN'], $domainArr) || (isset($info['host']) && in_array($info['host'], $domainArr))) {
                header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit;
            }
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');

            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                exit;
            }
        }
    }
}

/**
 * [GetNumberCode 随机数生成生成]
 * @param    [int] $length [生成位数]
 * @return   [int]         [生成的随机数]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 */
function GetNumberCode($length = 6)
{
    $code = '';
    for ($i = 0; $i < intval($length); $i++) $code .= rand(0, 9);
    return $code;
}

function CurlPost($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $info = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Errno' . curl_error($ch);
    }
    curl_close($ch);
    return $info;
}

/**
 * 参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：https请求不验证证书和hosts(不填则为HTTP)
 *
 * @param
 *            $url
 * @param string $data
 * @param string $http
 * @return mixed|string
 */
function GApiCurlExecute($url, $data = '', $header = '', $http = 1)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);//设置超时时间为1s
    if ($data) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    if ($http) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    if ($header) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    return $data;
}


function HttpGet($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: ' . 'application/json',
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

function HttpPost($url, $data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: ' . 'application/json',
        ),
        CURLOPT_POSTFIELDS => json_encode($data),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

/**
 * [GetNumberCode 随机10位字符串]
 * @param    [int] $length [生成位数]
 * @return   [int]         [生成的随机数]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 */
function GetNumberStr($num = 10)
{
    $num1 = $num + 1;
    $strs = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
    $name = substr(str_shuffle($strs), mt_rand(0, strlen($strs) - $num1), $num);
    return strtolower($name);
}

/**
 * 处理银行卡号每四位一个空格
 * @param $str
 * @return string
 */
function formart_bank_number($str)
{
    if (!$str) {
        return $str;
    }
    //通过正则，每四位截取到数组中
    preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/', $str, $match);
    $str = '';
    unset($match[0]); //去除掉第一个键，因为第一个键是银行卡号的完整卡号
    //通过循环，将字符串进行拼接并加入空格
    foreach ($match as $vo) {
        $str .= $vo . ' ';
    }
    return $str;
}

function isIp($ip)
{
    if (preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip)) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param $num         科学计数法字符串
 * @param int $double 小数点保留位数 默认10位
 * @return string
 */
function sctonum($num, $double = 6)
{
    if (stripos($num, "e") !== false) {
        $a = explode('e', strtolower($num));
        $str = bcmul($a[0], bcpow(10, $a[1], $double), $double);
        $num = rtrim(rtrim($str, '0'), '.');   //去除小数后多余的0
    }
    return $num;
}

if (!function_exists('readKey')) {
    function readKey()
    {
        $file_path = ROOT_PATH . 'app/.env';
        if (file_exists($file_path)) {
            $str = file_get_contents($file_path);
        }
        return $str;
    }
}

if (!function_exists('aes_encrypt')) {
    function aes_encrypt($data)
    {
        $key = readKey();
        //$key = "HelloWorld";
        $iv = 'ftyj8AYDcvSLcAiJ';
        if ($key == "") {
            return $data;
        }
        $text = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($text);
    }
}

if (!function_exists('aes_decrypt')) {
    function aes_decrypt($text)
    {
        $key = readKey();
        //$key = "HelloWorld";
        $iv = 'ftyj8AYDcvSLcAiJ';
        if ($key == "") {
            return $text;
        }
        $decodeText = base64_decode($text);
        $data = openssl_decrypt($decodeText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $data;
    }
}


if (!function_exists('base58_encode')) {
    function base58_encode($string)
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789abcdefghijkmnopqrstuvwxyz';
        $base = strlen($alphabet);
        if (is_string($string) === false) {
            return false;
        }
        if (strlen($string) === 0) {
            return '';
        }
        $bytes = array_values(unpack('C*', $string));
        $decimal = $bytes[0];
        for ($i = 1, $l = count($bytes); $i < $l; $i++) {
            $decimal = bcmul($decimal, 256);
            $decimal = bcadd($decimal, $bytes[$i]);
        }
        $output = '';
        while ($decimal >= $base) {
            $div = bcdiv($decimal, $base, 0);
            $mod = bcmod($decimal, $base);
            $output .= $alphabet[$mod];
            $decimal = $div;
        }
        if ($decimal > 0) {
            $output .= $alphabet[$decimal];
        }
        $output = strrev($output);
        foreach ($bytes as $byte) {
            if ($byte === 0) {
                $output = $alphabet[0] . $output;
                continue;
            }
            break;
        }
        // return aes_encrypt($output);
        return (string)$output;
    }
}
if (!function_exists('base58_decode')) {
    function base58_decode($base58)
    {
        // $base58 = aes_decrypt($base58Enc);
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789abcdefghijkmnopqrstuvwxyz';
        $base = strlen($alphabet);
        if (is_string($base58) === false) {
            return false;
        }
        if (strlen($base58) === 0) {
            return '';
        }
        $indexes = array_flip(str_split($alphabet));
        $chars = str_split($base58);
        foreach ($chars as $char) {
            if (isset($indexes[$char]) === false) {
                return false;
            }
        }
        $decimal = $indexes[$chars[0]];
        for ($i = 1, $l = count($chars); $i < $l; $i++) {
            $decimal = bcmul($decimal, $base);
            $decimal = bcadd($decimal, $indexes[$chars[$i]]);
        }
        $output = '';
        while ($decimal > 0) {
            $byte = bcmod($decimal, 256);
            $output = pack('C', $byte) . $output;
            $decimal = bcdiv($decimal, 256, 0);
        }
        foreach ($chars as $char) {
            if ($indexes[$char] === 0) {
                $output = "\x00" . $output;
                continue;
            }
            break;
        }
        return $output;
    }
}
if (!function_exists('hexstr_to')) {
    function hexstr_to($base58)
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789abcdefghijkmnopqrstuvwxyz';
        $base = strlen($alphabet);
        if (is_string($base58) === false) {
            return false;
        }
        if (strlen($base58) === 0) {
            return '';
        }
        $indexes = array_flip(str_split($alphabet));
        $chars = str_split($base58);
        foreach ($chars as $char) {
            if (isset($indexes[$char]) === false) {
                return false;
            }
        }
        $decimal = $indexes[$chars[0]];
        for ($i = 1, $l = count($chars); $i < $l; $i++) {
            $decimal = bcmul($decimal, $base);
            $decimal = bcadd($decimal, $indexes[$chars[$i]]);
        }
        $output = '';
        while ($decimal > 0) {
            $byte = bcmod($decimal, 256);
            $output = pack('C', $byte) . $output;
            $decimal = bcdiv($decimal, 256, 0);
        }
        foreach ($chars as $char) {
            if ($indexes[$char] === 0) {
                $output = "\x00" . $output;
                continue;
            }
            break;
        }
        return $output;
    }
}


function randomFloat($min = 0, $max = 1)
{
    $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    return sprintf("%.2f", $num);
}

//生成接口签名
function get_signature($data, $appsecret)
{
    foreach ($data as $key => $value) {
        if ($key == "signature" || $value == '') {
            unset($data[$key]);
        }
    }
    ksort($data);
    $sign = strtoupper(md5(urldecode(http_build_query($data)) . '&appsecret=' . $appsecret));
    return $sign;
}

//获取usdt实时价格
function get_usdt_cny($type = 2)
{
    switch ($type) {
        case '1':
            $type = 'USD';
            return 1;
            break;
        case '2':
            $type = 'CNY';
            break;
        case '3':
            $type = 'INR';
            break;
        case '4':
            $type = 'JPY';
            break;
        case '5':
            $type = 'KRW';
            break;
        case '6':
            $type = 'PHP';
            break;
        case '7':
            $type = 'EUR';
            break;
        case '8':
            $type = 'GBP';
            break;
        case '9':
            $type = 'CHF';
            break;
        case '10':
            $type = 'TWD';
            break;
        case '11':
            $type = 'HKD';
            break;
        case '12':
            $type = 'MOP';
            break;
        case '13':
            $type = 'SGD';
            break;
        case '14':
            $type = 'NZD';
            break;
        case '15':
            $type = 'THB';
            break;
        case '16':
            $type = 'CAD';
            break;
        default:
            $type = 'error';
            break;
    }
    //先去缓存获取
    $usdt_cny = Cache::get("usdt_" . $type);
    //如果有 则直接返回
    if (!empty($usdt_cny)) {
        return $usdt_cny;
    }
    $url = "https://webapi.huilv.cc/api/exchange?num=1&chiyouhuobi=USD&duihuanhuobi=" . $type;
    $data = [];
    $result = GApiCurlExecute($url, $data);
    $res = json_decode($result, true);
    if (empty($res) || $res['dangqianhuilv'] <= 0) {
        return 0;
    }
    $usdt_cny = floor($res['dangqianhuilv'] * 100) / 100;
    Cache::set('usdt_' . $type, $usdt_cny, 1800);

    if ($type == 'INR') {
        $urlInr = 'https://api.wazirx.com/api/v2/tickers/usdtinr';
        $dataInr = [];
        $resultInr = GApiCurlExecute($urlInr, $dataInr);
        $resInr = json_decode($resultInr, true);
        if (empty($resInr) || $resInr['ticker']['sell'] <= 0) {
            return 0;
        }
        $usdt_cny = floor($resInr['ticker']['sell'] * 100) / 100;
        Cache::set('usdt_' . $type, $usdt_cny, 1800);
    }

    return $usdt_cny;
    //return 0;
}

//判断是否是TRC20地址
function is_trc_address($address)
{
    try {
        $tron = new \IEXBase\TronAPI\Tron();
        $result = $tron->isAddress($address);
        return $result;
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        //echo $e->getMessage();
        return false;
    }
}

//判断是否是ETH20地址
function is_erc_address($address)
{
    if (!(preg_match('/^(0x)?[0-9a-fA-F]{40}$/', $address))) {
        return false; //满足if代表地址不合法
    }
    return true;
}

//生成TRC20地址  type=1表示TRC地址 2表示ETH地址  系统自动的
function create_address($type = 0)
{
    try {
        $data = [];
        if ($type == 1) {
            $tron = new \IEXBase\TronAPI\Tron();
            $generateAddress = $tron->generateAddress(); // or createAddress()
            $RawData = $generateAddress->getRawData();
            if (empty($RawData)) {
                return ['code' => '-1', 'msg' => '生成新地址失败'];
            }
            $data['address'] = $RawData['address_base58'];
            $data['address_hex'] = $RawData['address_hex'];
            $data['private_key'] = base58_encode($RawData['private_key']);
            $data['public_key'] = base58_encode($RawData['public_key']);
        } elseif ($type == 2) {
            $wallet = \kgsweb3\Wallet::create();//生成一个新的钱包
            if (empty($wallet->getAddress())) {
                return ['code' => '-1', 'msg' => '生成新地址失败'];
            }
            $data['address'] = $wallet->getAddress();//输出钱包的地址
            $data['private_key'] = base58_encode($wallet->getPrivateKey()); //输出钱包的私钥
        } else {
            return ['code' => '-1', 'msg' => '地址类型错误'];
        }
        // 生成二维码
        require_once root_path() . "vendor/phpqrcode/phpqrcode.php";
        $qRcode = new \QRcode();
        $dir = "phpqrcode/" . date('Y-m-d');
        if (!is_dir($dir)) mkdir($dir);
        $data['img'] = '/' . $dir . '/' . time() . rand(1111, 9999) . '.jpg';
        $imgdata = $data['address'];//网址或者是文本内容
        // 纠错级别：L、M、Q、H
        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
        // 生成的文件名
        $outfile = root_path() . "public" . $data['img']; //保存二维码的路径 false=不生成文件
        $qRcode->png($imgdata, $outfile, $level, $size);
        //存入数据库
        $data['type'] = $type;
        $data['status'] = 1;
        $data['usdt_balance'] = 0;
        $data['allocation_time'] = time();
        $data['create_time'] = time();
        $data['update_time'] = time();
        $addressmodel = new \app\common\model\Address();
        $address_id = $addressmodel->insertGetId($data);
        if ($address_id) {
            $data['id'] = $address_id;
            return ['code' => '1', 'msg' => '生成新地址成功', 'data' => $data];
        }
        return ['code' => '-1', 'msg' => '新地址入库失败'];
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
}

//生成TRC20地址  type=1表示TRC地址 2表示ETH地址   给商户提供的接口
function create_merchant_address($type, $merchant_id, $merchantname)
{
    try {
        $data = [];
        if ($type == 1) {
            $tron = new \IEXBase\TronAPI\Tron();
            $generateAddress = $tron->generateAddress(); // or createAddress()
            $RawData = $generateAddress->getRawData();
            if (empty($RawData)) {
                return ['code' => '-1', 'msg' => '生成新地址失败'];
            }
            $data['address'] = $RawData['address_base58'];
            $data['address_hex'] = $RawData['address_hex'];
            $data['private_key'] = base58_encode($RawData['private_key']);
            $data['public_key'] = base58_encode($RawData['public_key']);
        } elseif ($type == 2) {
            $wallet = \kgsweb3\Wallet::create();//生成一个新的钱包
            if (empty($wallet->getAddress())) {
                return ['code' => '-1', 'msg' => '生成新地址失败'];
            }
            $data['address'] = $wallet->getAddress();//输出钱包的地址
            $data['private_key'] = base58_encode($wallet->getPrivateKey()); //输出钱包的私钥
        } else {
            return ['code' => '-1', 'msg' => '地址类型错误'];
        }
        // 生成二维码
        require_once root_path() . "vendor/phpqrcode/phpqrcode.php";
        $qRcode = new \QRcode();
        $data['img'] = "/phpqrcode/" . time() . rand(1111, 9999) . '.jpg';
        $imgdata = $data['address'];//网址或者是文本内容
        // 纠错级别：L、M、Q、H
        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
        // 生成的文件名
        $outfile = root_path() . "public" . $data['img']; //保存二维码的路径 false=不生成文件
        $qRcode->png($imgdata, $outfile, $level, $size);
        //存入数据库
        $data['type'] = $type;
        $data['merchant_id'] = $merchant_id;
        $data['merchantname'] = $merchantname;
        $data['status'] = 1;
        $data['usdt_balance'] = 0;
        $data['allocation_time'] = time();
        $data['create_time'] = time();
        $data['update_time'] = time();
        $merchantaddressmodel = new \app\common\model\MerchantAddress();
        $address_id = $merchantaddressmodel->insertGetId($data);
        if ($address_id) {
            $data['id'] = $address_id;
            return ['code' => '1', 'msg' => '生成新地址成功', 'data' => $data];
        }
        return ['code' => '-1', 'msg' => '新地址入库失败'];
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
}

//获取trx余额
function get_trx_balance($address)
{
    $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
    $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
    $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
    try {
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
    $tron->setAddress($address);
    return ['code' => '1', 'msg' => '获取TRX余额成功', 'data' => $tron->getBalance(null, true)];
}

//获取eth余额
function get_eth_balance($address)
{
    $infura = sysconfig('site', 'infura');
    if (empty($infura)) {
//        $infura = 'c8cad4ec2e5a4e0d927925d8caf35c9b';
        $infura = env('infura.secret', '');
    }
    $web3 = new \kgsweb3\Web3('https://mainnet.infura.io/v3/' . $infura);
    //查询余额
    try {
        $balance = \kgsweb3\Utils::weiToEth($web3->getBalance($address)); //输出账户的以太坊余额
        return ['code' => '1', 'msg' => '获取ETH余额成功', 'data' => $balance];
    } catch (\Exception $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
}

//获取TRC20的usdt余额
function get_usdt_balance($address)
{
    try {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
    //如果是trc地址 则查trc的usdt余额
    if (is_trc_address($address)) {
        try {
            $tron = new Tron($fullNode, $solidityNode, $eventServer, null, null);
            $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/
            return ['code' => '1', 'msg' => '获取USDT余额成功', 'data' => $contract->balanceOf($address)];
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
    }
    //如果eth地址 则查eth的usdt余额
    if (is_erc_address($address)) {
        $abi = config('app.eth_abi');
        $infura = sysconfig('site', 'infura');
        if (empty($infura)) {
//            $infura = 'c8cad4ec2e5a4e0d927925d8caf35c9b';
            $infura = env('infura.secret', '');
        }
        $web3 = new \kgsweb3\Web3('https://mainnet.infura.io/v3/' . $infura);
        //合约的地址，即erc20代币的地址
        $contractAddress = '0xdac17f958d2ee523a2206206994597c13d831ec7';
        try {
            //根据web3，abi，以及合于地址初始化合约
            $contract = \kgsweb3\Contract::at($web3, $abi, $contractAddress);
            $res = $contract->call('balanceOf', [$address]);
            $balance = floatval((\kgsweb3\Utils::hexToDec($res)) / 1000000);
            return ['code' => '1', 'msg' => '获取USDT余额成功', 'data' => $balance];
        } catch (\Exception $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
    }
    return ['code' => '-1', 'msg' => '地址错误'];
}

//TRC20转账TRX
function transfer_trx($from_address, $to_address, $amount)
{

    if ($from_address == $to_address) {
        return ['code' => '-1', 'msg' => "地址不能相同"];
    }
    $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
    $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
    $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');

    try {
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
    //需要的最低TRX余额
    $TRX = sysconfig('riskconfig', 'min_trx');
    //查询转出地址的TRX余额
    $address_trx = get_trx_balance($from_address);//地址TRX余额
    //查看TRX余额是否足够 小于所需能量+要转账的金额
    $change_money = $TRX + $amount;
    if ($address_trx['data'] < $change_money) {
        return ['code' => '-1', 'msg' => "TRX余额小于最低需要的金额"];
    }
    //根据地址 查找出私钥 并解密
    $addressmodel = new \app\common\model\Address();
    $private_key = $addressmodel->where("address", $from_address)->value("private_key");
    if (empty($private_key)) {
        return ['code' => '-1', 'msg' => "私钥不存在"];
    }
    //还要判断距离上一次转的时间不能小于2分钟
    $to_res = $addressmodel->where("address", $to_address)->find();
    if ($to_res['transfer_trx_time'] > (time() - 100)) {
        return ['code' => '-1', 'msg' => "当前TRX正在转入，等待到账，稍后再执行"];
    }
    $private_key = base58_decode($private_key);
    $tron->setAddress($from_address);
    $tron->setPrivateKey($private_key);
    try {
        $transfer = $tron->send($to_address, floatval($amount));
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
    if (is_array($transfer) && $transfer['result'] === true) {
        return ['code' => '1', 'msg' => "TRX转账成功"];
    }
    return ['code' => '-1', 'msg' => "TRX转账失败"];
}

//ETH20转账ETH
function transfer_eth($from_address, $to_address, $amount)
{
    if ($from_address == $to_address) {
        return ['code' => '-1', 'msg' => "地址不能相同"];
    }
    //需要的最低TRX余额
    $ETH = sysconfig('riskconfig', 'min_eth');
    //查询转出地址的ETH余额
    $address_eth = get_eth_balance($from_address);//地址ETH余额
    //查看ETH余额是否足够 小于所需能量+要转账的金额
    $change_money = $ETH + $amount;
    if ($address_eth['data'] < $change_money) {
        return ['code' => '-1', 'msg' => "ETH余额小于最低需要的金额"];
    }
    //根据地址 查找出私钥 并解密
    $addressmodel = new \app\common\model\Address();
    $private_key = $addressmodel->where("address", $from_address)->value("private_key");
    if (empty($private_key)) {
        return ['code' => '-1', 'msg' => "私钥不存在"];
    }
    $private_key = base58_decode($private_key);
    try {
        $infura = sysconfig('site', 'infura');
        if (empty($infura)) {
//            $infura = 'c8cad4ec2e5a4e0d927925d8caf35c9b';
            $infura = env('infura.secret', '');
        }
        $client = new \kgsweb3\Client('https://mainnet.infura.io/v3/' . $infura);
        $client->addPrivateKeys([$private_key]);
        $trans = [
            "from" => $from_address,
            "to" => $to_address,
            "value" => \kgsweb3\Utils::ethToWei($amount, true),
            "data" => '0x',
        ];
        $trans['gas'] = dechex(hexdec($client->eth_estimateGas($trans)) * 1.5);
        $trans['gasPrice'] = $client->eth_gasPrice();
        $trans['nonce'] = $client->eth_getTransactionCount($from_address, 'pending');
        $txid = $client->sendTransaction($trans);
        return ['code' => '1', 'msg' => "ETH提交转账成功", 'data' => $txid];
    } catch (\Exception $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
    return ['code' => '-1', 'msg' => "ETH提交转账失败"];
}

//TRC20和ERC20转账USDT
function transfer_usdt($from_address, $to_address, $money, $type, $change_order_id = 0, $change_order_sn = 0)
{
    if (is_trc_address($from_address) && is_trc_address($to_address)) {
        return transfer_trc20_usdt($from_address, $to_address, $money, $type, $change_order_id, $change_order_sn);
    }
    if (is_erc_address($from_address) && is_erc_address($to_address)) {
        return transfer_erc20_usdt($from_address, $to_address, $money, $type, $change_order_id, $change_order_sn);
    }
    return ['code' => '-1', 'msg' => "地址错误"];
}

//TRC20转账USDT
function transfer_trc20_usdt($from_address, $to_address, $money, $type, $change_order_id = 0, $change_order_sn = 0)
{

    //根据地址 查找出私钥 并解密
    $transferdmodel = new \app\common\model\AddressTransfer();
    $addressmodel = new \app\common\model\Address();
    $private_key = $addressmodel->where("address", $from_address)->value("private_key");
    if (empty($private_key)) {
        return ['code' => '-1', 'msg' => "私钥不存在"];
    }
    if ($from_address == $to_address) {
        return ['code' => '-1', 'msg' => "转入和转出地址不能相同"];
    }
    $private_key = base58_decode($private_key);
    try {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        //查询相应的能量够不够
        $trx_balance = get_trx_balance($from_address);
        if ($trx_balance['code'] != 1) {
            return ['code' => '-1', 'msg' => "获取地址TRX余额失败"];
        }
        $min_trx = sysconfig('riskconfig', 'min_trx');
        if ($trx_balance['data'] < $min_trx) {
            //能量不够 则转入能量到该地址  找出TRX足够的地址
            $enough_address = $addressmodel->getEnoughAddress($min_trx, 1);
            if (empty($enough_address)) {
                return ['code' => '-1', 'msg' => "所有地址TRX余额不足"];
            }
            //这里转入的是最小需要的trx减去余额里面有的
            $zhuanru = $min_trx - $trx_balance['data'] + 0.01;
            $zhuanru = sprintf("%.2f", $zhuanru);
            $ress = transfer_trx($enough_address, $from_address, $zhuanru);
            if ($ress['code'] != 1) {
                return ['code' => '-1', 'msg' => $ress['msg']];
            }
            return ['code' => '-1000', 'msg' => "转账所需TRX不够，稍后再执行"];
        }
        //查询转出地址的USDT余额
        $address_usdt = get_usdt_balance($from_address);//地址USDT余额
        if ($address_usdt['code'] != 1 || $address_usdt['data'] < $money) {
            //如果查询不出来余额 则跳过这个  或者usdt余额小于转账金额 也跳过
            return ['code' => '-1', 'msg' => "地址USDT余额查询失败，或者小于转账金额"];
        }
        $tron = new Tron($fullNode, $solidityNode, $eventServer, null, null, $private_key);
        $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/
        //如果交易成功 则记录交易 返回成功
        //记录交易
        $transferData = $contract->transfer($to_address, $money, $from_address);
        if (!isset($transferData['result'])) {
            return ['code' => '-1', 'msg' => "转账失败，可能是秘钥错误"];
        }
        if ($transferData['result'] === true) {
            $transaction_id = $transferData['txid'];
        }
        $recordData['transaction_id'] = $transaction_id;
        $recordData['from_address'] = $from_address;
        $recordData['to_address'] = $to_address;
        $recordData['money'] = $money;
        $recordData['change_order_id'] = $change_order_id;
        $recordData['change_order_sn'] = $change_order_sn;
        $res1 = $transferdmodel->recordTransfer($recordData, $type);
        return ['code' => '1', 'msg' => "发送交易成功", 'data' => $transferData];
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
}

//ERC20转账USDT
function transfer_erc20_usdt($from_address, $to_address, $money, $type, $change_order_id = 0, $change_order_sn = 0)
{
    //根据地址 查找出私钥 并解密
    $transferdmodel = new \app\common\model\AddressTransfer();
    $addressmodel = new \app\common\model\Address();
    $private_key = $addressmodel->where("address", $from_address)->value("private_key");
    if (empty($private_key)) {
        return ['code' => '-1', 'msg' => "私钥不存在"];
    }
    if ($from_address == $to_address) {
        return ['code' => '-1', 'msg' => "转入和转出地址不能相同"];
    }
    $private_key = base58_decode($private_key);
    try {
        //查询相应的能量够不够
        $eth_balance = get_eth_balance($from_address);
        if ($eth_balance['code'] != 1) {
            return ['code' => '-1', 'msg' => "获取地址ETH余额失败"];
        }
        $min_eth = sysconfig('riskconfig', 'min_eth');
        if ($eth_balance['data'] < $min_eth) {
            //能量不够 则转入能量到该地址  找出ETH足够的地址
            $enough_address = $addressmodel->getEnoughAddress($min_eth, 2);
            if (empty($enough_address)) {
                return ['code' => '-1', 'msg' => "所有地址ETH余额不足"];
            }
            $ress = transfer_eth($enough_address, $from_address, $min_eth);
            if ($ress['code'] != 1) {
                return ['code' => '-1', 'msg' => $ress['msg']];
            }
            return ['code' => '-1', 'msg' => "转账所需ETH不够，稍后再执行"];
        }
        //查询转出地址的ETH余额和USDT余额
        $address_usdt = get_usdt_balance($from_address);//地址USDT余额
        if ($address_usdt['code'] != 1) {
            //如果查询不出来余额 则跳过这个  或者usdt余额小于转账金额 也跳过
            return ['code' => '-1', 'msg' => "地址USDT余额查询失败"];
        }
        if ($address_usdt['data'] < $money) {
            //如果查询不出来余额 则跳过这个  或者usdt余额小于转账金额 也跳过
            return ['code' => '-1', 'msg' => "地址USDT余额小于转账金额"];
        }

        //合约的地址，即erc20代币的地址
        $contractAddress = '0xdac17f958d2ee523a2206206994597c13d831ec7';
        $infura = sysconfig('site', 'infura');
        if (empty($infura)) {
//            $infura = 'c8cad4ec2e5a4e0d927925d8caf35c9b';
            $infura = env('infura.secret', '');
        }
        $client = new \kgsweb3\Client('https://mainnet.infura.io/v3/' . $infura);
        $client->addPrivateKeys([$private_key]);
        $trans = [
            "from" => $from_address,
            "to" => $contractAddress,
            "value" => "0x0",
        ];
        $hash = Keccak::hash("transfer(address,uint256)", 256);
        $hash_sub = mb_substr($hash, 0, 8, 'utf-8');
        $fill_from = \kgsweb3\Utils::fill0(\kgsweb3\Utils::remove0x($to_address));//收款地址加密
        //转账金额  usdt位数6位
        $num10 = $money * 1000000;
        $num16 = \kgsweb3\Utils::decToHex($num10);
        $count = \kgsweb3\Utils::fill0(\kgsweb3\Utils::remove0x($num16));//转账金额加密后
        //拼接 data 字符串
        $trans['data'] = "0x" . $hash_sub . $fill_from . $count;
        $trans['gas'] = "0x" . dechex(hexdec($client->eth_estimateGas($trans)) * 1.5);
        $trans['gasPrice'] = $client->eth_gasPrice();
        $trans['nonce'] = $client->eth_getTransactionCount($from_address, 'pending');

        $transaction_id = $client->sendTransaction($trans);
        if (empty($transaction_id)) {
            return ['code' => '-1', 'msg' => "发送交易失败"];
        }

        // TODO: 修复$transferData变量未定义以及address transfer未保存的bug
        $recordData['transaction_id'] = $transaction_id;
        $recordData['from_address'] = $from_address;
        $recordData['to_address'] = $to_address;
        $recordData['money'] = $money;
        $recordData['change_order_id'] = $change_order_id;
        $recordData['change_order_sn'] = $change_order_sn;
        $res1 = $transferdmodel->recordTransfer($recordData, $type);
        if ($res1) {
            return ['code' => '1', 'msg' => "发送交易成功", 'data' => $trans];
        } else {
            return ['code' => '-1', 'msg' => "保存交易记录失败"];
        }
    } catch (\Exception $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
}

//根据id查询交易
function find_transfer($transaction_id, $chain_type)
{
    if ($chain_type == 1) {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');

        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
        try {
            $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/
            $detail = $tron->getTransaction($transaction_id);
            $result = $contract->getTransaction($transaction_id);
            if (is_array($detail) && $detail['ret']['0']['contractRet'] === "SUCCESS" && $tron->fromHex(preg_replace('/^0+/', '', $result['contract_address'])) == "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t") {
                $from_address = preg_replace('/^0+/', '', $result['log']['0']['topics']['1']);
                $to_address = preg_replace('/^0+/', '', $result['log']['0']['topics']['2']);
                if (strlen($from_address) % 2) {
                    $from_address = str_pad($from_address, 40, 0, STR_PAD_LEFT);
                }
                if (strlen($from_address) == 40) {
                    $from_address = '41' . $from_address;
                }
                if (strlen($from_address) == 41) {
                    $from_address = '4' . $from_address;
                }
                if (strlen($from_address) == 43) {
                    $from_address = substr_replace($from_address, 41, 0, 3);
                }
                if (strlen($to_address) % 2) {
                    $to_address = str_pad($to_address, 40, 0, STR_PAD_LEFT);
                }
                if (strlen($to_address) == 40) {
                    $to_address = '41' . $to_address;
                }
                if (strlen($to_address) == 41) {
                    $to_address = '4' . $to_address;
                }
                if (strlen($to_address) == 43) {
                    $to_address = substr_replace($to_address, 41, 0, 3);
                }
                if (empty($result['fee'])) {
                    $result['fee'] = 0;
                }
                $returndata = array(
                    'contract_address' => $tron->fromHex(preg_replace('/^0+/', '', $result['contract_address'])),
                    'from_address' => $tron->fromHex($from_address),
                    'to_address' => $tron->fromHex($to_address),
                    'amount' => hexdec(preg_replace('/^0+/', '', $result['log']['0']['data'])) / 1000000,
                    'fee' => $tron->fromTron($result['fee']),
                    'time' => $result['blockTimeStamp'] / 1000,
                );
                return ['code' => '1', 'msg' => "交易成功", 'data' => $returndata];
            }
            return ['code' => '-1', 'msg' => "交易失败"];
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
    }
    if ($chain_type == 2) {
        try {
            $infura = sysconfig('site', 'infura');
            if (empty($infura)) {
//                $infura = 'c8cad4ec2e5a4e0d927925d8caf35c9b';
                $infura = env('infura.secret', '');
            }
            $client = new \kgsweb3\Client('https://mainnet.infura.io/v3/' . $infura);
            $result = $client->eth_getTransactionReceipt($transaction_id);

            $result2 = $client->eth_getBlockByHash($result->blockHash, false);
            if (!empty($result) && hexdec($result->status) == 1 && $result->to == "0xdac17f958d2ee523a2206206994597c13d831ec7") {
                $temp1 = $result->logs;
                $temp2 = $temp1['0'];
                $temp3 = $temp2->topics;
                $gas_used = hexdec(\kgsweb3\Utils::remove0x($result->gasUsed));
                $gas_price = hexdec(\kgsweb3\Utils::remove0x($result->effectiveGasPrice)) / 1000000000;
                $returndata = array(
                    'contract_address' => $result->to,
                    'from_address' => $result->from,
                    'to_address' => '0x' . (preg_replace('/^0+/', '', \kgsweb3\Utils::remove0x($temp3['2']))),
                    'amount' => hexdec(preg_replace('/^0+/', '', \kgsweb3\Utils::remove0x($temp2->data))) / 1000000,
                    'fee' => ($gas_used * $gas_price) / 1000000000,
                    'time' => hexdec(\kgsweb3\Utils::remove0x($result2->timestamp)),
                );
                return ['code' => '1', 'msg' => "交易成功", 'data' => $returndata];
            }
            return ['code' => '-1', 'msg' => "交易失败"];
        } catch (\Exception $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
    }
    return ['code' => '-1', 'msg' => '地址类型错误'];
}


/*function find_withdraw_transfer($transaction_id, $chain_type)
{
    if ($chain_type == 1) {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');

        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
        try {
            $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/
            $detail = $tron->getTransaction($transaction_id);
            $result = $contract->getTransaction($transaction_id);
            // TODO:
            return ['code' => '-1', 'msg' => json_encode($detail)];
            if (is_array($detail) && $detail['ret']['0']['contractRet'] === "SUCCESS" && $tron->fromHex(preg_replace('/^0+/', '', $result['contract_address'])) == "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t") {
                $from_address = preg_replace('/^0+/', '', $result['log']['0']['topics']['1']);
                $to_address = preg_replace('/^0+/', '', $result['log']['0']['topics']['2']);
                if (strlen($from_address) % 2) {
                    $from_address = str_pad($from_address, 40, 0, STR_PAD_LEFT);
                }
                if (strlen($from_address) == 40) {
                    $from_address = '41' . $from_address;
                }
                if (strlen($from_address) == 41) {
                    $from_address = '4' . $from_address;
                }
                if (strlen($from_address) == 43) {
                    $from_address = substr_replace($from_address, 41, 0, 3);
                }
                if (strlen($to_address) % 2) {
                    $to_address = str_pad($to_address, 40, 0, STR_PAD_LEFT);
                }
                if (strlen($to_address) == 40) {
                    $to_address = '41' . $to_address;
                }
                if (strlen($to_address) == 41) {
                    $to_address = '4' . $to_address;
                }
                if (strlen($to_address) == 43) {
                    $to_address = substr_replace($to_address, 41, 0, 3);
                }
                if (empty($result['fee'])) {
                    $result['fee'] = 0;
                }
                $returndata = array(
                    'contract_address' => $tron->fromHex(preg_replace('/^0+/', '', $result['contract_address'])),
                    'from_address' => $tron->fromHex($from_address),
                    'to_address' => $tron->fromHex($to_address),
                    'amount' => hexdec(preg_replace('/^0+/', '', $result['log']['0']['data'])) / 1000000,
                    'fee' => $tron->fromTron($result['fee']),
                    'time' => $result['blockTimeStamp'] / 1000,
                );
                return ['code' => '1', 'msg' => "交易成功", 'data' => $returndata];
            }
            return ['code' => '-1', 'msg' => "交易失败"];
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
    }
    if ($chain_type == 2) {
        try {
            $infura = sysconfig('site', 'infura');
            if (empty($infura)) {
//                $infura = 'c8cad4ec2e5a4e0d927925d8caf35c9b';
                $infura = env('infura.secret', '');
            }
            $client = new \kgsweb3\Client('https://mainnet.infura.io/v3/' . $infura);
            $result = $client->eth_getTransactionReceipt($transaction_id);
            $result2 = $client->eth_getBlockByHash($result->blockHash, false);
            if (!empty($result) && hexdec($result->status) == 1 && $result->to == "0xdac17f958d2ee523a2206206994597c13d831ec7") {
                $temp1 = $result->logs;
                $temp2 = $temp1['0'];
                $temp3 = $temp2->topics;
                $gas_used = hexdec(\kgsweb3\Utils::remove0x($result->gasUsed));
                $gas_price = hexdec(\kgsweb3\Utils::remove0x($result->effectiveGasPrice)) / 1000000000;
                $returndata = array(
                    'contract_address' => $result->to,
                    'from_address' => $result->from,
                    'to_address' => '0x' . (preg_replace('/^0+/', '', \kgsweb3\Utils::remove0x($temp3['2']))),
                    'amount' => hexdec(preg_replace('/^0+/', '', \kgsweb3\Utils::remove0x($temp2->data))) / 1000000,
                    'fee' => ($gas_used * $gas_price) / 1000000000,
                    'time' => hexdec(\kgsweb3\Utils::remove0x($result2->timestamp)),
                );
                return ['code' => '1', 'msg' => "交易成功", 'data' => $returndata];
            }
            return ['code' => '-1', 'msg' => "交易失败"];
        } catch (\Exception $e) {
            return ['code' => '-1', 'msg' => $e->getMessage()];
        }
    }
    return ['code' => '-1', 'msg' => '地址类型错误'];
}*/

//查询TRC20和ERC20交易列表
function select_transfer($address, $min_time = 0, $max_time = 3648806636, $chain_type = 1)
{
    if ($chain_type == 1 && !is_trc_address($address)) {
        return ['code' => '-1', 'msg' => "地址" . $address . "不是TRC20地址"];
    } else if ($chain_type == 2 && !is_erc_address($address)) {
        return ['code' => '-1', 'msg' => "地址" . $address . "不是ERC20地址"];
    }
    if (is_trc_address($address)) {
        return select_transfer_trc20($address, $min_time, $max_time, $chain_type);
    }
    if ($chain_type == 2 && is_erc_address($address)) {
        return select_transfer_etherscan($address, $min_time, $max_time, $chain_type);
    }
    return ['code' => '-1', 'msg' => "地址错误"];
}

//查询TRC20交易列表
function select_transfer_trc20($address, $min_time = 0, $max_time = 3648806636, $chain_type = 1)
{

    $min_time = $min_time * 1000;
    $max_time = $max_time * 1000;
    try {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
    try {
        $tron = new Tron($fullNode, $solidityNode, $eventServer, null, null);
        $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/
        $result = $contract->getTransactions($address, 200, $min_time, $max_time);
        if ($result['success'] != true || empty($result['data'])) {
            return ['code' => '-1', 'msg' => "没有数据"];
        }
        $returndata = [];
        foreach ($result['data'] as $key => $value) {
            if ($value['block_timestamp'] >= $min_time && $value['block_timestamp'] <= $max_time && $value['token_info']['address'] == 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t' && $value['type'] == 'Transfer') {
                $returndata[$key]['contract_address'] = $value['token_info']['address'];
                $returndata[$key]['transaction_id'] = $value['transaction_id'];
                $returndata[$key]['from_address'] = $value['from'];
                $returndata[$key]['to_address'] = $value['to'];
                $returndata[$key]['time'] = $value['block_timestamp'] / 1000;
                $returndata[$key]['money'] = $value['value'] / 1000000;
            }
        }
        return ['code' => '1', 'msg' => "获取成功", 'data' => $returndata];
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        return ['code' => '-1', 'msg' => $e->getMessage()];
    }
}

function select_transfer_etherscan($address, $min_time = 0, $max_time = 3648806636, $chain_type = 2)
{
    $apikey = "WPRF8A6ZX5X1CD4EDJNANWWNWXRD3R74EB";
    $reqUrl = "https://api.etherscan.io/api?module=account&action=tokentx&address=$address&page=1&offset=100&startblock=0&endblock=27025780&sort=desc&apikey=$apikey";
    $resp = GApiCurlExecute($reqUrl);
    $resp = json_decode($resp, true);
    $returndata = [];
    if ($resp['status'] == 1 && !empty($resp['result'])) {
        foreach ($resp['result'] as $key => $value) {
            if ($value['timeStamp'] >= $min_time && $value['timeStamp'] <= $max_time && $value['contractAddress'] == "0xdac17f958d2ee523a2206206994597c13d831ec7") {
                $returndata[$key]['contract_address'] = $value['contractAddress'];
                $returndata[$key]['transaction_id'] = $value['hash'];
                $returndata[$key]['from_address'] = $value['from'];
                $returndata[$key]['to_address'] = $value['to'];
                $returndata[$key]['time'] = $value['timeStamp'];
                $returndata[$key]['money'] = $value['value'] / 1000000;
            }
        }
        return ['code' => '1', 'msg' => "获取成功", 'data' => $returndata];
    } else {
        return ['code' => '-1', 'msg' => "获取交易记录失败, " . $resp['message'] . ''];
    }
}
