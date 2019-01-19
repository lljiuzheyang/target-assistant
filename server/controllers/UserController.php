<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/18
 * Time: 上午11:46
 */

namespace app\controllers;


use app\helper\ResultHelper;
use app\service\user\UserService;

class UserController extends BaseController
{

    public function actionLogin($code)
    {
        $user = UserService::instance();
        $data = $user->authorization($code);

        return $this->json($data);
    }

}