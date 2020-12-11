<?php


namespace AllInPay\AllInPay\ServiceProviders;


use Modules\Core\Services\AllInPay\Src\SignContract\SignContract;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SignContractServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['signContract'] = function ($pimple) {
            return new SignContract();
        };
    }
}