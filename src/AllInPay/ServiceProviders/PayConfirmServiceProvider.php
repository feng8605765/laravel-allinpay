<?php


namespace AllInPay\AllInPay\ServiceProviders;


use AllInPay\PayConfirm\PayConfirm;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PayConfirmServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['payConfirm'] = function ($pimple) {
            return new PayConfirm();
        };
    }
}