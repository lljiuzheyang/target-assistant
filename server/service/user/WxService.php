<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/19
 * Time: 下午8:25
 */

namespace app\service\user;

use app\helper\CryptHelper;
use app\helper\CurlHelper;
use app\helper\ResultHelper;
use app\service\BaseService;

class WxService extends BaseService
{
    private $appid;
    private $appsecret;

    public function __construct()
    {
        $this->appid = \Yii::$app->params['wx']['appid'];
        $this->appsecret = \Yii::$app->params['wx']['appsecret'];
    }

    const URL_JSCODE2SESSION =
        'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';

    /**
     * code转session
     * 正确的情况下返回一维数组 | [openid,sessionKey,unionid]
     * @param string $code
     * @return array|bool
     */
    public function code2session(string $code)
    {
        try {
            $url = sprintf(self::URL_JSCODE2SESSION, $this->appid, $this->appsecret, $code);
            $response = CurlHelper::get($url);
            // 腾讯微信真是不遵循文档走的，智能先这样子(说的的errcode判断结果，成功后却没有errcode)
            //  {"session_key":"O3hsnRFTO+4rKtLgVeJ8ag==","openid":"oUnip5ZdepJ-Ar5RmGzPrSz3UJzk"}
            $ret = json_decode($response);

            if (empty($ret)) throw new \Exception('code转sessionKey异常');

            $message = [
                'openid' => $ret['openid'],
                'sessionKey' => $ret['session_key'],
                'unionid' => $ret['unionid'] ?? ''
            ];
            return ResultHelper::generate(ResultHelper::成功, null, $message);
        } catch (\Exception $e) {
            return ResultHelper::generate(ResultHelper::code转sessionKey异常,
                $e->getMessage()
            );
        }
        catch (\Error $error){
            return ResultHelper::generate(ResultHelper::code转sessionKey异常,
                $error->getMessage()
            );
        }

    }

    /**
     * 加密sessionKey信息
     * 将加密后的密文回传小程序端保存，后续小程序业务需要走开发者服务器解密信息
     * @param array $sessionInfo
     * @return string
     */
    public static function ensessionKey(array $sessionInfo)
    {
        $key = json_encode($sessionInfo, JSON_UNESCAPED_UNICODE);
        return CryptHelper::encrypt($key);
    }

}