# zhongqian-laravel
This package is only tested on Laravel 5.2
## Installation
1) Intall it via composer
```shell
composer require hxshandle/zhongqian-laravel
```
2) Open your `config/app.php` and add the following to the `providers` array:
```php
HXS\ZQ\ZQServiceProvider::class,
```
3) Public Config
```shell
php artisian vendor:publish
```