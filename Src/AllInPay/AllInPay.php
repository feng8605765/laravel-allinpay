<?php

namespace AllInPay\AllInPay;

use AllInPay\AllInPay\ServiceProviders\PayConfirmServiceProvider;
use AllInPay\AllInPay\ServiceProviders\WithdrawServiceProvider;
use AllInPay\AllInPay\ServiceProviders\BalanceServiceProvider;
use AllInPay\AllInPay\ServiceProviders\RefundServiceProvider;
use AllInPay\AllInPay\ServiceProviders\SignContractServiceProvider;
use Pimple\Container;

class AllInPay extends Container
{
    protected $providers = [
        WithdrawServiceProvider::class,
        SignContractServiceProvider::class,
        RefundServiceProvider::class,
        BalanceServiceProvider::class,
        PayConfirmServiceProvider::class
    ];

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->registerProviders();
    }

    /**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }


    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

}
