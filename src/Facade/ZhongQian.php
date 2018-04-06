<?php
/**
 * Created by PhpStorm.
 * User: I073349
 * Date: 4/6/2018
 * Time: 11:32 AM
 */

namespace HXS\ZQ\Facade;


use Illuminate\Support\Facades\Facade;

class ZhongQian extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zhongqian';
    }
}