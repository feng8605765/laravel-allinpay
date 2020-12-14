<?php

class Config
{
    protected static $config;

    /**
     * 加载支付配置文件
     * @param $configFile
     */
    public function loadConfig($configFile)
    {
        if (config('allinpay')) {
            self::$config = config('allinpay');
        } elseif (__DIR__.'/'.$configFile) {
            self::$config = require ($configFile);
        }
    }

    /**
     * 进行单例的处理
     * @return Config
     */
    public static function getInstance()
    {
        static $object;

        if (!isset($object)) {
            $object = new Config();
        }

        return $object;
    }

    /**
     * 获取对应的配置名字
     * @param $name
     * @return string
     */
    public function getConf($name)
    {
        if (isset(self::$config[$name])) {
            return self::$config[$name];
        } else {
            return " config $name is undefined";
        }
    }


}
