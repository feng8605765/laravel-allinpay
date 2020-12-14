<?php


namespace AllInPay\AllInPay\Facades;


use Illuminate\Support\Facades\Facade;

class AllInPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    /**
     * 默认为 Server
     *
     * @return string
     */
    static public function getFacadeAccessor()
    {
        return "allinpay";
    }

    /**
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    static public function __callStatic($name, $args)
    {
        $app = static::getFacadeRoot();

        if (method_exists($app, $name)) {
            return call_user_func_array([$app, $name], $args);
        }

        return $app->$name;
    }
}