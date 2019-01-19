<?php

namespace app\helper;

use yii;
use app\enums\CodeEnum;

/**
 * 平台统一返回结果工具，所有错误类型在这里记录
 *
 * @author zengsiyuan
 */
class ResultHelper {

    /**
     * 结果状态码字段名
     */
    const CODE_FIELD = 'errcode';

    /**
     * 结果信息字段名
     */
    const MSG_FIELD = 'errmsg';

    /**
     * 结果数据字段名
     */
    const DATA_FIELD = 'data';

    /**
     * 兼容文件上传字段
     * 上传状态
     */
    const STATE = 'state';

    /**
     * 兼容文件上传字段
     * 上传URL
     */
    const URL = 'url';

    /**
     * 兼容文件上传字段
     * 上传title
     */
    const TITLE = 'title';

    /**
     * 兼容文件上传字段
     * 上传源
     */
    const ORIGINAL = 'original';

    /**
     * 根据状态码获取结果信息
     * @param int $code 状态码
     * @param string $msg 结果信息，如果标准库存在，则会合并标准消息和$msg
     * @param boolean $rawMessage 是否按照$msg原样输出结果描述，如果为false将会在标准库寻找对应的code并拼接至结果描述中
     * @return string 结果信息
     */
    private static function getErrmsg(int $code, string $msg = null, $rawMessage = false) {
        if ($rawMessage && !empty($msg)) {
            return $msg;
        }

        return implode(',', array_filter([static::$resultDict[$code], $msg], function($var) {
            return !empty($var);
        }));
    }

    /**
     * 生成平台标准格式结果
     * @param int $code 结果状态码，如果状态码在标准库里面存在，则会在errmsg中显示标准结果信息
     * @param string $msg 结果描述，如果标准库里存在标准结果信息，则会追加在标准结果信息之后，以逗号分隔
     * @param mixed $data 业务结果数据
     * @param boolean $rawMsg 是否按照$msg原样输出结果描述，如果为false将会在标准库寻找对应的code并拼接至结果描述中
     * @return array 生成的结果数组
     */
    public static function generate(int $code, string $msg = null, $data = null, $rawMsg = false) {
        static::init();

        return [
            static::CODE_FIELD => $code,
            static::MSG_FIELD => static::getErrmsg($code, $msg, $rawMsg),
            static::DATA_FIELD => $data
        ];
    }

    /**
     * 生成平台兼容图片格式结果
     * @param int $code 结果状态码，如果状态码在标准库里面存在，则会在errmsg中显示标准结果信息
     * @param string $msg 结果描述，如果标准库里存在标准结果信息，则会追加在标准结果信息之后，以逗号分隔
     * @param string $url 图片链接
     * @param string $title 图片标题
     * @param string $original 图片源名
     * @param boolean $rawMsg 是否按照$msg原样输出结果描述，如果为false将会在标准库寻找对应的code并拼接至结果描述中
     * @return array 生成的结果数组
     */
    public static function generateUpload(int $code, string $msg = null, string $url = null, string $title = '', string $original = '', $rawMsg = false) {
        static::init();

        return [
            static::STATE => $rawMsg ? $msg : static::getErrmsg($code, $msg),
            static::URL => $url,
            static::TITLE => $title,
            static::ORIGINAL => $original
        ];
    }

    /**
     * 根据异常生成标准格式结果
     * @param \Throwable $ex 异常信息
     * @return array 生成的结果数组
     */
    public static function generateFromException(\Throwable $ex) {
        $errcode = $ex->getCode();
        return static::generate($errcode === 0 ? static::失败 : $errcode, $ex->getMessage(), null, true);
    }

    /**
     * 抛出标准异常
     * @param int $code 状态码
     * @param string $msg 异常信息，如果标准库存在，则会合并标准消息和$msg
     * @param boolean $rawMessage 是否按照$msg原样输出结果描述，如果为false将会在标准库寻找对应的code并拼接至结果描述中
     * @throws \Exception 标准异常
     */
    public static function throwException(int $code, string $msg = null, $rawMessage = false) {
        static::init();

        $errmsg = static::getErrmsg($code, $msg, $rawMessage);
        $exception = new \Exception($errmsg, $code);
        yii::error($exception->getMessage());
        yii::error($exception->getCode());
        yii::error($exception->getTraceAsString());
        throw $exception;
    }

    /**
     * 获取未截断的TraceAsString
     *
     * @param $exception \Exception
     * @return string
     */
    public static function getExceptionTraceAsString($exception)
    {
        $rtn = "";
        $count = 0;
        foreach ($exception->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = array();
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    } elseif (is_array($arg)) {
                        $args[] = "Array";
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }
            $rtn .= sprintf("#%s %s(%s): %s(%s)\n",
                $count,
                array_key_exists('file', $frame)?$frame['file']:'',
                array_key_exists('line', $frame)?$frame['line']:'',
                array_key_exists('function', $frame)?$frame['function']:'',
                $args);
            $count++;
        }
        return $rtn;
    }

    /**
     * 根据标准结果判断是否抛出异常，如果结果不为成功则抛出异常，否则什么也不做
     * @param array $result 获取到的标准结果数组,例如['errcode' => 0 , 'errmsg' => 'xxx']
     * @throws \Exception 如果结果不为成功，则抛出异常
     */
    public static function throwExceptionOnResultError(array $result) {
        if (!static::isValidResult($result)) {
            static::throwException(static::错误的结果格式);
        } else if ($result[static::CODE_FIELD] !== static::成功) {
            static::throwException($result[static::CODE_FIELD], $result[static::MSG_FIELD], true);
        }
    }

    /**
     * 校验是否为标准的结果格式
     * @param array $resultData 待校验的结果数据
     * @return boolean 是否为标准结果格式
     */
    public static function isValidResult($resultData) {
        return is_array($resultData) && array_key_exists(static::CODE_FIELD, $resultData);
    }

    /**
     * 判断结果的状态是否等于预期
     * @param string $resultData 结果数据
     * @param int $errcode 预期结果状态码
     * @return boolean 判断结果
     */
    public static function isErrcodeEqualTo($resultData, $errcode) {
        return static::isValidResult($resultData) && $resultData[static::CODE_FIELD] === $errcode;
    }

    /**
     * 判断结果是否成功
     * @param array $resultData 结果数据
     * @return boolean 是否成功
     */
    public static function isSucceed($resultData) {
        return static::isErrcodeEqualTo($resultData, static::成功);
    }

    const 成功 = 0;
    const 失败 = 1;
    const 未登录 = 2;
    const 无权访问 = 3;
    const 参数错误 = 4;
    const 错误的结果格式 = 5;
    const 接收到错误的数据格式 = 10000;
    const 不存在的类名 = 10001;
    const 错误的命名空间 = 10002;
    const 必须传入方法名 = 10003;
    const 解析服务调用异常 = 10004;
    const 获取JSON请求数据失败 = 10005;
    const code转sessionKey异常 = 201;
//    const TCP连接远程服务器失败 = 20001;
//    const 向远程服务器发送数据失败 = 20002;
//    const 接收远程服务器返回数据失败 = 20003;
//    const 远程服务器返回了错误的数据格式 = 20004;
//    const 远程HTTP服务器响应了错误的状态码 = 20005;
//    const 远程连接被意外关闭 = 20006;
//    const MQ消息TAG不能为空 = 30001;
//    const MQ生产者代理返回了错误的数据格式 = 30002;
//    const 与MQ生产者代理通信过程中出现异常 = 30003;
//    const MQ消息TAG只能包含小写字母和下划线 = 30004;
//    const 记录MQ消息日志异常 = 30101;
//    const 接受者WEB控制器只能在YII_DEBUG模式下使用 = 40001;
//    const 未设置ACCESSTOKEN = 50001;
//    const ACCESSTOKEN未包含UUID = 50002;
//    const 错误的TOKEN类型 = 50003;
//    //K8S相关异常
//    const 任务名称不能为空 = 60001;
//    const K8S接口响应异常 = 60002;
//    const 容器数据不能为空 = 60003;
//    const 计划任务周期配置不能为空 = 60004;
//    const 模块配置不存在 = 60005;
//    const 模块镜像地址不能为空 = 60006;
//    const 模块镜像启动命令不能为空 = 60007;
//    const 容器组名称不能为空 = 60008;
//    //计划中心相关异常
//    const 方法名错误 = 70001;
//    const WORKER不存在 = 70002;
//    const 方法定义错误 = 70003;
//    const 任务执行失败 = 70004;
//    //shell脚本执行异常
//    const SHELL脚本执行异常 = 80001;

    /**
     * 初始化标准库，将当前类和继承类的常量放入数组中
     */
    protected static function init() {
        $currentClass = get_called_class();

        if (array_key_exists($currentClass, static::$initializedClass)) {
            return;
        }

        $reflection = new \ReflectionClass($currentClass);

        $currentDict = array_flip($reflection->getConstants());

        static::$resultDict = static::$resultDict + $currentDict;

        static::$initializedClass[$currentClass] = true;
    }

    /**
     * 静态是否已经初始化了
     * @var boolean
     */
    protected static $initializedClass = [];

    /**
     * 标准返回状态库，不同业务状态码首位数字应有所区别
     *
     * @var array 标准状态库数组
     */
    protected static $resultDict = [];

    /**
     * 返回json格式
     * @param $code
     * @param null $data
     * @return array
     */
    public static function getInfo($code, $data = null) {
        if ($code == 0) {
            return array(static::CODE_FIELD => $code, static::MSG_FIELD => CodeEnum::getMsg($code), static::DATA_FIELD => $data);
        } else {
            return array(static::CODE_FIELD => $code, static::MSG_FIELD => CodeEnum::getMsg($code));
        }
    }

}
