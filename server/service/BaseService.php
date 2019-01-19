<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/19
 * Time: 下午8:20
 */

namespace app\service;

/**
 * 服务基类
 *
 * @author 刘富胜
 */

use yii\base\Component;
use yii\base\StaticInstanceTrait;

abstract class BaseService extends Component
{
    use StaticInstanceTrait;

    private $ServiceContext;

    public function __construct($config = []) {
        parent::__construct($config);
        $this->ServiceContext = \Yii::$container->get('app\service\ServiceContext');
    }

    /**
     * 服务上下文
     *
     * @return ServiceContext
     */
    public function getServiceContext() {
        return $this->ServiceContext;
    }



}