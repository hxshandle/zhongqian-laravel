<?php
/**
 * Created by PhpStorm.
 * User: I073349
 * Date: 4/6/2018
 * Time: 10:34 AM
 */

namespace HXS\ZQ;


use Illuminate\Support\ServiceProvider;

class ZQServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => app()->basePath() . '/config/zhongqian.php',
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerZQ();
        $this->mergeConfig();
    }

    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'zhongqian'
        );
    }

    private function registerZQ()
    {
        $this->app->bind('zhongqian', function ($app) {
            return new ZhongQian();
        });

        $this->app->alias('zq', 'HXS\ZQ\ZhongQian');
    }
}