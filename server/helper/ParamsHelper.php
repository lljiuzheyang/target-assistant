<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/18
 * Time: 上午11:30
 */

namespace app\helper;

use yii;

class ParamsHelper
{
    /**
     * 获取int类型GET参数
     * @param string $key URL参数名称
     * @return int int类型值
     */
    public static function getInt($key) {
        return intval(\Yii::$app->request->get($key));
    }

    /**
     * 获取float类型GET参数
     * @param string $key URL参数名称
     * @return float float类型值
     */
    public static function getFloat($key) {
        return floatval(\Yii::$app->request->get($key));
    }

    /**
     * 获取过滤html字符类型GET参数
     * @param string $key URL参数名称
     * @return string html字符过滤
     */
    public static function getString($key) {
        return \Yii::$app->request->get($key);
    }

    /**
     * 获取int类型POST参数
     * @param string $key URL参数名称
     * @return int int类型值
     */
    public static function postInt($key) {
        return intval(\Yii::$app->request->post($key));
    }

    /**
     * 获取float类型POST参数
     * @param string $key URL参数名称
     * @return float float类型值
     */
    public static function postFloat($key) {
        return floatval(\Yii::$app->request->post($key));
    }

    /**
     * 获取过滤html字符类型POST参数
     * @param string $key URL参数名称
     * @return string html字符过滤
     */
    public static function postString($key) {
        return \Yii::$app->request->post($key);
    }

    /**
     * 获取请求的JSON数据，适合以JSON格式作为请求体的场景
     * @param boolean $assoc 结果是否转化为数组
     * @return mixed 根据JSON数据转换的对象
     */
    public static function postJsonArray($assoc = true) {

        if (!yii::$app->request->isPost) {
            return null;
        }

        $json = file_get_contents('php://input');

        $result = json_decode($json, $assoc, 512, JSON_BIGINT_AS_STRING);

        if (is_null($result)) {
            ResultHelper::throwException(10005, $json);
        }

        return $result;
    }
}