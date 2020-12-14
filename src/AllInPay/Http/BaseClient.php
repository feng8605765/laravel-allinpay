<?php

namespace AllInPay\AllInPay\Http;

use AllInPay\AllInPay\Concerns\PaymentRules;

class BaseClient
{
    use PaymentRules;

    protected $bizUserId;

    /**
     * 规则条件
     *
     * @var string[]
     */
    protected $rules = [
        'stopPay',  // 停止支付
        'forbidBlackCompany',
        'forbidBlackUser',
    ];

    /**
     * 支付
     * BaseClient constructor.
     */
    public function __construct()
    {
    }

    /**
     * 设置类属性
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $attribute) {
            $this->{$key} = $attribute;
        }
    }

    /**
     * 注册支付规则
     *
     * @param array $attributes
     */
    public function registerRules(array $attributes)
    {
        $this->setAttributes($attributes);

        foreach ($this->rules as $rule) {
            call_user_func_array([$this, $rule], []);
        }
    }
}
