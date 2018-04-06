<?php
/**
 * Created by PhpStorm.
 * User: I073349
 * Date: 4/6/2018
 * Time: 10:18 AM
 */
return [
    "private_key" => env('ZQ_PRIVATE_KEY', null),
    "public_key" => env('ZQ_PUBLIC_KEY', null),
    // 众签唯一标示
    "zqid" => env('ZQ_ID', null),
    "push_user_notify_callback" => env('ZQ_PUSH_USER_NOTIFY_CALLBACK', null),
    "zq_domain" => env('ZQ_DOMAIN', 'test.sign.zqsign.com:8081'),
    'zq_contract_name' => env('ZQ_CONTRACT_NAME', '埃欧健身合同')
];