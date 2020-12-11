<?php

namespace AllInPay\AllInPay\ServiceProviders;

use Modules\Core\Services\AllInPay\Src\Refund\Refund;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RefundServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['refund'] = function ($pimple) {
            return new Refund();
        };
    }
}
