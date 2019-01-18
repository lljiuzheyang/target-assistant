<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/18
 * Time: 上午11:35
 */

namespace app\enums;

class CodeEnum
{
    /**
     * 返回json格式
     * @param $code
     * @return string
     */
    public static function getMsg($code)
    {
        $arr = [
            0 => '成功',
            1 => '失败',
            2 => '未登录',
            3 => '无权访问',
            4 => '参数错误',
            5 => '错误的结果格式',
            10000 => '接收到错误的数据格式',
            10001 => '不存在的类名',
            10002 => '错误的命名空间',
            10003 => '必须传入方法名',
            10004 => '解析服务调用异常',
            10005 => '获取JSON请求数据失败',

        ];
        return $arr[$code];
    }
}