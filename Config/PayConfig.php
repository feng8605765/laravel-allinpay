<?php

return [
    /**
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug' => true,

    'stop' => false,

    'api_url' => 'http://test.allinpay.com/op/gateway',

    // 平台分配的APPID
    'app_id' => env('ALLINPAY_APPID','123456789'),

    'sys_id' => '2002141050372732927',

    // 实际交易的商户号
    'cusid' => '6666666',

    // 集团商户号
    'orgid' => '6666666',

    // 私钥
    'private_key' => base_path().'/config/pay/'.env('ALLINPAY_PRIVATE_KEY',''),

    // 公钥
    'public_key' => env('ALLINPAY_PUBLIC_KEY',''),

    // 版本
    'pay_api_version' => env('ALLINPAY_API_VERSION','v1.0'),

    // 密码
    'pwd' => env('ALLINPAY_CERTIFICATE_PASSWORD',123456),

    // 密码key
    'secretKey' => env('ALLINPAY_SECRETKEY',12312312),

    // 版本
    'version' => '1.0',

    'tlCertPath' => base_path().'/config/pay/TLCert-test.cer'

];