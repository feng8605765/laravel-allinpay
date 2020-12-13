<?php

namespace AllInPay\AllInPay\ServiceProviders;

use AllInPay\Balance\Balance;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BalanceServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['balance'] = function ($pimple) {
            return new Balance();
        };
    }
}
