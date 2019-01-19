<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/19
 * Time: 下午8:40
 */

namespace app\helper;


class CryptHelper
{
    const CIPHER_MODE = 'aes-128-ecb';

    // 加密
    public static function encrypt($input)
    {
        $cihperRaw = openssl_encrypt($input, self::CIPHER_MODE, \Yii::$app->params['wx']['cryptkey'], OPENSSL_RAW_DATA);
        return base64_encode($cihperRaw);
    }

    // 解密
    public static function decrypt($cipherText)
    {
        $chiperRaw = base64_decode($cipherText);
        $content   = openssl_decrypt($chiperRaw, self::CIPHER_MODE, \Yii::$app->params['wx']['cryptkey'], OPENSSL_RAW_DATA);
        return $content;
    }
}