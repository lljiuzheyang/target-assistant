<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/18
 * Time: 上午11:46
 */

namespace app\controllers;


use app\helper\ResultHelper;

class UserController extends BaseController
{
    public function actionLogin($code)
    {
        return $this->json(ResultHelper::generate(ResultHelper::成功,null, $code));
    }

}