<?php
/**
 * Created by PhpStorm.
 * User: I073349
 * Date: 4/6/2018
 * Time: 3:11 PM
 */

namespace HXS\ZQ;


class Helper
{
    public static function curlPost($url, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        //设置返回值
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        //得到结果
        $result = curl_exec($ch);
        curl_close($ch); //关闭curl
        return $result;
    }

    public static function buildQuery($query)
    {
        if (!$query) {
            return null;
        }
        //将要 参数 排序
        ksort($query);
        //重新组装参数
        $params = array();
        foreach ($query as $key => $value) {
            $params[] = $key . '=' . $value;
        }
        $data = implode('&', $params);
        return $data;
    }

    public static function zqSign($query = array(), $privateKey)
    {
        if (!is_array($query)) {
            return null;
        }
        //排序参数，
        $data = self::buildQuery($query);
        // 私钥密码
        $passphrase = '';
        $key_width = 64;
        $p_key = array();
        //如果私钥是 1行
        if (!stripos($privateKey, "\n")) {
            $i = 0;
            while ($key_str = substr($privateKey, $i * $key_width, $key_width)) {
                $p_key[] = $key_str;
                $i++;
            }
        } else {
            //echo '一行？';
        }

        //将一行代码
        $privateKey = "-----BEGIN PRIVATE KEY-----\n" . implode("\n", $p_key);
        $privateKey = $privateKey . "\n-----END PRIVATE KEY-----";
        $pkeyid = openssl_get_privatekey($privateKey);
        openssl_sign($data, $sign, $pkeyid);
        openssl_free_key($pkeyid);
        $sign = base64_encode($sign);
        return $sign;
    }

    public static function generateSN()
    {
        $sn = date('ymd').substr(time(),-5).substr(microtime(),2,5).rand(10,99);
        return $sn;
    }
}