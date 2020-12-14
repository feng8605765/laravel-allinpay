<?php

namespace AllInPay\AllInPay;

use AllInPay\AllInPay\Console\TestAllInPayCommand;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;

class AllInPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->setupConfig($this->app);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/PayConfig.php',
            'allinpay'
        );

        $this->app->singleton('single-allinpay', function ($app) {
            return new AllInPay();
        });

        // 设置通联测试脚本
        if ($this->app->runningInConsole()) {
            $this->commands(
                TestAllInPayCommand::class
            );
        }
    }

    /**
     * 提供的服务
     *
     * @return array|string[]
     */
    public function provides()
    {
        return [AllInPay::class, 'allinpay'];
    }

    /**
     * 设置配置
     *
     * @param Application $app
     */
    protected function setupConfig(Application $app)
    {
        $configPath = realpath(__DIR__ . '/../../config/PayConfig.php');

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$configPath => config_path('payConfig.php')]);
        } elseif ($app instanceof \Laravel\Lumen\Application) {
            $app->configure('allinpay');
        }

        $this->mergeConfigFrom($configPath, 'allinpay');
    }
}
