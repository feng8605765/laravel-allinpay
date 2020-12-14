# 通联支付Laravel

## 概述

通联支付所有业务支持

## 运行环境
- PHP 7.1 +
- laravel/lumen-framework 5.8.x

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

## Lumen应用

1. 在`bootstrap/app.php`的 80 行左右： 
```php
    $app->register(\AllInPay\AllInPay\AllInPayServiceProvider::class);
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
