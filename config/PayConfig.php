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
    'app_id' => env('ALLINPAY_APPID','1581648210684'),

    'sys_id' => '2002141050372732927',

    // 实际交易的商户号
    'cusid' => '6666666',

    // 集团商户号
    'orgid' => '6666666',

    // 私钥
    'private_key' => '',

    // 公钥
    'public_key' => '../data/TLCert-test.cer',
    
    // 应用私钥证书密码
    'pwd' => env('ALLINPAY_CERTIFICATE_PASSWORD',123456),

    // 密码key
    'secretKey' => 'WaHVZNHZYX3v4si1bBTVseIwEMPMcKzL',

    // 版本
    'version' => 'v1.0',
];