# 通联支付Laravel

## 概述

通联支付所有业务支持

## 要求
| 依赖 | 说明 |
| -------- | -------- |
| [PHP](https://secure.php.net/manual/zh/install.php) | `>= 7.1.0` `推荐PHP7+` |
| [Laravel](https://laravel.com/)/[Lumen](https://lumen.laravel.com/) | `>=5.8` `推荐5.8+` | 

提示：
- 需要在laravel 5.8以上版本使用

## 安装方法

1. 如果您通过composer管理您的项目依赖，可以在你的项目根目录运行：
```
$ composer require allinpay/laravel-allinpay
```

或者在您的 composer.json中声明对allinpay/laravel-allinpay for php的依赖：
 ```
    "require": {
        "allinpay/laravel-allinpay": "~1.0"
    }   
 ```
然后通过`composer install` 安装依赖。composer安装完成后,在您的php代码中引入依赖即可：
```
    require_once __DIR__.'/vendor/autoload.php';
```

## 配置
1. 注册 `ServiceProvider`:
```php
    AllInPay\AllInPay\AllInPayServiceProvider::class,
```

2. 配置文件修改在config/allinpay.php 中对应的修改配置
```php
    [
        // 停止支付
        'stop' => false, 
        // 当前为测试请求url
        'api_url' => 'http://test.allinpay.com/op/gateway', 
        // 通商云的appId
        'app_id' => env('ALLINPAY_APPID','1581648210684'), 
        // 对应平台账户sysid
        'sys_id' => '2002141050372732927',
        // 应用私钥证书 
        'private_key' => '../data/1581648210684.pfx',  
        // 公钥路径
        'public_key' => '../data/TLCert-test.cer',
        // 应用私钥证书密码
        'pwd' => env('ALLINPAY_CERTIFICATE_PASSWORD',123456),
        // 密码
        'secretKey' => 'WaHVZNHZYX3v4si1bBTVseIwEMPMcKzL',
        // 版本
        'version' => 'v1.0',
        // 用户支付黑名单
        'user_blacks' => [],
        // 企业支付黑名单
        'company_blacks' => [],
    ]
```

3. 配置log日志在config/logging.php中增加对应的log配置
```php
    'channels' => [
        'allinpay' => [
            // 生成的日志规则按月生成
            'driver' => 'daily', 
            // 生成的目录路径
            'path' => storage_path('logs/allinpay/allinpay.log'),  
            // 日志等级
            'level' => 'info', 
            // 保存时间
            'days' => 14,  
        ]       
    ]   
```

## Lumen应用

1. 在`bootstrap/app.php`的 80 行左右： 
```php
    $app->register(\AllInPay\AllInPay\AllInPayServiceProvider::class);
```

2. 执行 command 命令测试通联接口请求：
```php
    php artisan script:test-allinpay
```

## 快速使用

```php
 ### 查询余额
 app('allinpay')->balance->balance($request);
 
### 查询退款账户
 app('allinpay')->balance->queryReserveFundBalance();

### 提现申请
 app('allinpay')->withdraw->withdrawApply($request);
```
