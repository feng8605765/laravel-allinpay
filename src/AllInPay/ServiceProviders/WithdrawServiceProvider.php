<?php

namespace AllInPay\AllInPay\ServiceProviders;

use AllInPay\Withdraw\Withdraw;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WithdrawServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['withdraw'] = function ($pimple) {
            return new Withdraw();
        };
    }
}
