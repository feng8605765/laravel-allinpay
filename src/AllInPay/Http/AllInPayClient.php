<?php

namespace AllInPay\AllInPay\Http;

use AllInPay\AllInPay\Exceptions\Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;

require_once __DIR__.'/../../../Config/Config.php';


class AllInPayClient extends BaseClient
{
    /**
     * 业务
     */
    const BUSINESS = [
        'signContract' => '签约',
        'withdraw'     => '提现',
        'balance'      => '余额',
        'refund'       => '退款',
        'selectBalance' => '选择余额',
        'payByBackSMS' => '确认支付',
        'resendPaySMS' => '重发短信',
    ];

    /**
     * 通联id
     *
     * @var
     */
    public $bizUserId;

    /**
     * 方法
     *
     * @var string
     */
    protected $method;

    /**
     * 服务对象
     *
     * @var string
     */
    protected $service;

    /**
     * 支付配置
     *
     * @var array
     */
    protected $config;

    /**
     * 业务类型
     *
     * @var string
     */
    protected $business;

    /**
     * 日志
     *
     * @var
     */
    protected $log;

    public function __construct()
    {
        $this->config = \Config::getInstance();
        $this->config->loadConfig('PayConfig.php');

        parent::__construct();

        $this->log = \logger()->channel('all_in_pay');
    }

    /**
     * 发起通联云的请求
     *
     * @param array  $param  参数数组
     * @param string $method 支付服务对象
     *
     * @throws Exception
     *
     * @return bool
     */
    public function guzzleHttp(array $param, string $method)
    {
        $this->registerRules($param);

        $this->method = $method;

        $params = $this->formatRequest($param);

        $apiUrl = $this->config->getConf('api_url');

        try {
            $result = $this->requestTSYAPI($apiUrl, $params);

            return $this->validateApiResponse($result);
        } catch (Exception $exception) {
            $this->log->error('请求失败:', $params);

            throw $exception;
        }
    }

    /**
     * 获取url的后缀参数拼接
     *
     * @param array $param 请求参数
     *
     * @return string
     */
    public function concatUrlParams(array $param): string
    {
        $request = $this->formatUrlRequest($param);
        $sb      = '';

        $this->log->info("[业务:{$this->business}][请求状态]", $request);

        foreach ($request as $entryKey => $entryValue) {
            $sb .= $entryKey.'='.urlencode($entryValue).'&';
        }

        return trim($sb, '&');
    }

    /**
     * 格式化url请求链接拼接
     *
     * @param $param
     *
     * @throws
     *
     * @return array
     */
    public function formatUrlRequest(array $param): array
    {
        $urlRequest = [
            'appId'      => $this->config->getConf('app_id'),
            'method'     => $param['method'],
            'sysId'      => $this->config->getConf('sys_id'),
            'charset'    => 'utf-8',
            'format'     => 'JSON',
            'signType'   => 'SHA256WithRSA',
            'timestamp'  => Carbon::now()->toDateTimeString(),
            'version'    => $this->config->getConf('version'),
            'bizContent' => json_encode($param),
        ];

        $urlRequest['sign'] = $this->sign($urlRequest);

        return $urlRequest;
    }

    /**
     * 验证返回的数据成功性
     *
     * @param $result
     *
     * @throws
     *
     * @return bool | array
     */
    public function validateApiResponse($result)
    {
        $response = json_decode($result, true);

        $this->validateResponseCode($response);

        $sign = $response['sign'];
        unset($response['sign']);

        $this->asciiSort($response);
        $str = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);

        if (!empty($sign)) {
            $success = $this->verify($this->getPublicKeyPath(), $str, base64_decode($sign));
        }

        return isset($success) ? $response: false;
    }

    /**
     * [foo 对返回数据按照第一个字符的键值ASCII码递增排序]
     *
     * @param $arr
     */
    public function asciiSort(&$arr)
    {
        if (is_array($arr)) {
            ksort($arr);

            foreach ($arr as &$v) {
                $this->asciiSort($v);
            }
        }
    }

    /**
     * 商户秘钥
     *
     * @param $strRequest
     *
     * @throws Exception
     *
     * @return string
     */
    public function sign(array $strRequest): string
    {
        unset($strRequest['signType']);
        $strRequest = array_filter($strRequest);//剔除值为空的参数
        ksort($strRequest);
        $sb = '';

        foreach ($strRequest as $entry_key => $entry_value) {
            $sb .= $entry_key.'='.$entry_value.'&';
        }
        $sb = trim($sb, '&');

        $this->log->info("[业务：{$this->business}][待签名源串]".json_encode($sb));

        //MD5摘要计算,Base64
        $sb         = base64_encode(hash('md5', $sb, true));
        $privateKey = $this->getPrivateKey();

        if (openssl_sign(utf8_encode($sb), $sign, $privateKey, OPENSSL_ALGO_SHA256)) {//SHA256withRSA密钥加签

            @openssl_free_key($privateKey);

            return base64_encode($sign);
        }

        throw new Exception('签名错误 sign error');
    }

    /**
     * 验证回调的签名
     *
     * @param array $request 请求的数据
     *
     * @throws Exception
     *
     * @return bool
     */
    public function checkSign(array $request): bool
    {
        $requestSign = $request['sign'];

        unset($request['sign']);

        $sign = $this->sign($request);

        if ($sign != $requestSign) {
            $this->log->error("[业务：{$this->business}回调签名验证失败]".json_encode($request));

            return false;
        }

        return true;
    }

    /**
     *获取私匙的绝对路径;
     */
    public function getPrivateKey()
    {
        return $this->loadPrivateKey($this->config->getConf('private_key'), $this->config->getConf('pwd'));
    }

    /**
     * [encryptAES AES-SHA1PRNG加密算法]
     *
     * @param $string
     *
     * @return string
     */
    public function encryptAES($string): string
    {
        //AES加密通过SHA1PRNG算法
        $key  = substr(openssl_digest(openssl_digest($this->config->getConf('secretKey'), 'sha1', true), 'sha1', true), 0, 16);
        $data = openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);

        return strtoupper(bin2hex($data));
    }

    /**
     * [encryptAES AES-SHA1PRNG解密算法]
     */
    public function decryptAES($string)
    {
        $key = substr(openssl_digest(openssl_digest($this->config->getConf('secretKey'), 'sha1', true), 'sha1', true), 0, 16);

        return openssl_decrypt(hex2bin($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
    }

    /**
     * 获取不同配置的回调方法
     *
     * @param $backUrl
     *
     * @return string
     */
    public function envBackUrl(string $backUrl): string
    {
        return env('APP_DOMAIN').$backUrl;
    }

    /**
     * 获取通知方法
     *
     * @param $notifyUrl
     *
     * @return string
     */
    public function envNotifyUrl(string $notifyUrl): string
    {
        return env('APP_DOMAIN').$notifyUrl;
    }

    /**
     * 设置业务类型
     *
     * @param string $business 业务类型 签约: signContract 提现：withdraw 余额：balance
     *
     * @return $this|string
     */
    public function setBusiness(string $business): self
    {
        $this->business = self::BUSINESS[$business];

        return $this;
    }

    /**
     * 验证返回码
     *
     * @param $response
     *
     * @throws Exception
     */
    protected function validateResponseCode($response)
    {
        if (isset($response) && $response['code'] == '20000') {
            throw new Exception(json_encode($response));
        }
    }

    /**
     * 格式化请求的参数
     *
     * @param array $param 参数
     *
     * @throws
     *
     * @return array
     */
    protected function formatRequest(array $param): array
    {
        $request = [
            'appId'      => $this->config->getConf('app_id'),
            'method'     => $this->method,
            'charset'    => 'utf-8',
            'format'     => 'JSON',
            'signType'   => 'SHA256WithRSA',
            'timestamp'  => Carbon::now()->toDateTimeString(),
            'version'    => $this->config->getConf('version'),
            'bizContent' => json_encode($param, JSON_UNESCAPED_UNICODE),
        ];

        $request['sign'] = $this->sign($request);

        return $request;
    }

    /**
     * 获取公钥的绝对路径
     *
     * @return string
     */
    protected function getPublicKeyPath()
    {
        return $this->config->getConf('tlCertPath');
    }

    /**
     * 请求通商云的接口
     *
     * @param $apiUrl
     * @param $request
     * @param string $httpMethod
     *
     * @throws
     *
     * @return bool|string|void
     */
    protected function requestTSYAPI($apiUrl, $request, $httpMethod = 'POST'): string
    {
        $client = new Client();

        $result = $client->request($httpMethod, $apiUrl, ['form_params' => $request]);

        $statusCode = $result->getStatusCode();

        if ($statusCode == 200) {
            $body = $result->getBody();

            $this->log->info("[业务:{$this->business}][请求状态]", $request);

            $contents = $body->getContents();

            $this->log->info("[业务:{$this->business}][原始响应：]".$contents);

            return $contents;
        }

        throw new Exception('暂无响应');
    }

    /**
     * 验证返回的数据合法性
     *
     * @param $publicKeyPath
     * @param $text
     * @param $sign
     *
     * @return bool
     */
    private function verify($publicKeyPath, $text, $sign)
    {
        //MD5摘要计算
        $text     = base64_encode(hash('md5', $text, true));
        $pubKeyId = openssl_get_publickey(file_get_contents($publicKeyPath));
        $flag     = (bool) openssl_verify($text, $sign, $pubKeyId, 'sha256WithRSAEncryption');
        openssl_free_key($pubKeyId);

        \logger()->channel('all-in-pay')->info('[sign value]'.(bool) ($flag));

        return $flag;
    }

    /**
     * 从证书文件中装入私钥 pem格式;
     */
    private function loadPrivateKey($path, $pwd)
    {
        $str    = explode('.', $path);
        $suffix = $str[count($str) - 1];

        if ($suffix == 'pfx') {
            return $this->loadPrivateKeyByPfx($path, $pwd);
        }

        if ($suffix == 'pem') {
            $priKey = file_get_contents($path);
            $res    = openssl_get_privatekey($priKey, $pwd);

            if (!$res) {
                exit('您使用的私钥格式错误，请检查私钥配置');
            }

            return $res;
        }
    }

    /**
     * 从证书文件中装入私钥 Pfx 文件格式
     */
    private function loadPrivateKeyByPfx($path, $pwd)
    {
        if (file_exists($path)) {
            $priKey = file_get_contents($path);

            if (openssl_pkcs12_read($priKey, $certs, $pwd)) {
                return $certs['pkey'];
            }

            die('私钥文件格式错误');
        }

        die('私钥文件不存在');
    }
}
