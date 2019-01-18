<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/18
 * Time: 上午11:49
 */

namespace app\controllers;


use service\helper\ServiceContext;

class ServiceController extends BaseController
{
    /*
     * 服务名称具体由各个服务的服务控制器基类初始化构造前赋值
     */
    protected $serviceName;
    /*
     * 当前服务上下文
     */
    protected $serviceContext = null;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->diServiceContext();
    }

    /*
     * 注入服务上下文
     */
    private function diServiceContext(){
        $this->serviceName = \Yii::$app->id;

        $this->serviceContext = new ServiceContext();
        $this->serviceContext->serviceName = $this->serviceName;

        \Yii::$container->setSingleton('platform\service\ServiceContext', $this->serviceContext);
    }
}