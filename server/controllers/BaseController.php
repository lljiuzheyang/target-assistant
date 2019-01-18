<?php

namespace app\controllers;

use yii;
use app\helper\ResultHelper;
use yii\web\Response;
use app\helper\ParamsHelper;

/**
 * WEB基础控制器，提供一些基础工具，所有WEB类控制器应该基于此类实现
 *
 * @author 刘富胜
 */
class BaseController extends \yii\web\Controller {

    public function __construct($id, $module, $config = []) {
        //模拟阿里云网关的请求
        Yii::$app->response->headers->set('access-control-allow-headers', 'X-Requested-With,X-Sequence,X-Ca-Key,X-Ca-Secret,X-Ca-Version,X-Ca-Timestamp,X-Ca-Nonce,X-Ca-API-Key,X-Ca-Stage,X-Ca-Client-DeviceId,X-Ca-Client-AppId,X-Ca-Signature,X-Ca-Signature-Headers,X-Ca-Signature-Method,X-Forwarded-For,X-Ca-Date,X-Ca-Request-Mode,Authorization,Content-Type,Accept,Accept-Ranges,Cache-Control,Range,Content-MD5,Pragma');
        Yii::$app->response->headers->set('access-control-allow-methods', 'GET,POST,PUT,DELETE,HEAD,OPTIONS,PATCH');
        Yii::$app->response->headers->set('access-control-max-age', '172800');
        Yii::$app->response->headers->set('access-control-allow-origin', '*');

        if (yii::$app->request->isOptions) {
            Yii::$app->response->headers->set('content-length', '0');
            Yii::$app->response->statusCode = 200;
            Yii::$app->end();
        }
        if (YII_ENV_DEV) {
            setcookie('XDEBUG_SESSION', 'PHPSTORM', time() + 50000);
        }

        parent::__construct($id, $module, $config);
    }

    /**
     * 关闭CSRF验证
     * @var boolean
     */
    public $enableCsrfValidation = false;

    /**
     * 获取返回的JSON结果
     * @param mixed $data 待转化成JSON的数据
     * @return mixed $data 返回结果，传递给Controller的结果，将自动转化为JSON格式结果，例如：return $this->json(['abc' => '123']);
     */
    protected function json($data) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $data;
    }

    /**
     * 获取请求的JSON数据，适合以JSON格式作为请求体的场景
     * @param boolean $assoc 结果是否转化为数组
     * @return mixed 根据JSON数据转换的对象
     */
    protected function getDataFromRequestJson($assoc = true) {
        return ParamsHelper::postJsonArray($assoc);
    }

    /**
     * 以JSON格式返回统一格式的标准结果
     * @param int $code 结果状态码，如果状态码在标准库里面存在，则会在errmsg中显示标准结果信息
     * @param string $msg 结果描述，如果标准库里存在标准结果信息，则会追加在标准结果信息之后，以逗号分隔
     * @param mixed $data 业务结果数据
     * @return array 生成的结果数组
     */
    protected function result(int $code, string $msg = null, $data = null) {
        return $this->json(ResultHelper::generate($code, $msg, $data));
    }

}
