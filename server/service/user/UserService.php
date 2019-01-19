<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/19
 * Time: 下午8:36
 */

namespace app\service\user;


use app\helper\ResultHelper;
use app\service\BaseService;

class UserService extends BaseService
{

    /**
     * 小程序作为中间件位置，让服务器与微信服务器鉴权
     * 根据小程序wx.login得到的code生成鉴权态
     * @param $code
     * @return mixed
     */
    public function authorization($code)
    {
        $wxService = WxService::instance();
        $sessionInfo = $wxService->code2session($code);
        if ($sessionInfo['errcode'] != ResultHelper::成功){
            return $sessionInfo;
        }

        $result = ['sessionToken' => $wxService::ensessionKey($sessionInfo['data'])];
        return ResultHelper::generate(ResultHelper::成功,null,$result);
    }

}