<?php
/**
 * @name Utils_Trace
 * @desc Trace DEBUG等调试信息跟踪器
 * @other 静态调用，依托trace容器统一收集和调度
 * @author yinhongbo <yinhongbo@baidu.com>
 */
class Utils_Trace{

    // TRACE容器
    public static $traceContainer = array();

    // 开关
    public static $switch = 0;

    // 是否被调用，用于鉴别开关启用情况
    public static $isCall = 0;

    // 私钥
    public static $privateKey = '2011.12.30_V0.8';

    // 生命周期， 以S为单位，默认15分钟
    public static $lifeCycle = 900;

    // 是否要强制开启
    public static $force = 0;

    // 模块
    const MODULE = 'searchbox';

    static public function setTrace($module = '', $key = ''){
        if(self::getSwitch() == 0){
            return false;
        }
        $result1 = self::_setTraceData(debug_backtrace(), $module, 'debug_backtrace', 1);
        //$result2 = self::_setTraceData(get_defined_vars(), $module, 'get_defined_vars');
        return $result1 && $result2;
    }

    static public function setUserVars($data, $module = '', $key = '', $isArray = 1){
        if(self::getSwitch() == 0){
            return false;
        }
        $key = empty($key) ? md5( time().mt_rand(0,10000) ) : $key;
        return self::_setTraceData($data, $module, $key, $isArray);
    }

    static public function _setTraceData( $data, $module, $key, $isArray = 1){
        if( empty($data) ){
            return false;
        }
        $module = empty($module) ? self::MODULE : ''.$module;
        $key    = ''.$key;

        if( 1 == $isArray ){
            self::$traceContainer[$module][$key][] = $data;
        }else{
            self::$traceContainer[$module][$key] = $data;
        }
        return true;
    }

    static public function getTrace(){
        if(self::getSwitch() == 0){
            return array();
        }
        self::setBaseTrace();
        return self::$traceContainer;
    }

    static public function setBaseTrace(){
        if(self::getSwitch() == 0){
            return false;
        }
        self::_setTraceData($_SERVER, '', 'sys_server', 0);
        self::_setTraceData(base64_encode(file_get_contents('php://input')), '', 'sys_php_input', 0);
        return true;
    }

    static public function getSwitch(){
        if( self::$isCall == 1){
            return self::$switch;
        }else{
            self::$isCall == 1;
        }
        // 静态类/方法暂直接使用原生GET/POST变量
        $requestParams = array_merge($_GET,$_POST);
        if( isset($requestParams['trace']) && !empty($requestParams['trace']) ){
            $traceArr = explode('_', $requestParams['trace']);
            if( is_array($traceArr) && count($traceArr) >= 2 ){
                $sign = md5(self::$privateKey.$traceArr[0]);
                // 取MD5的 偶数位的前八位
                $realSign = $sign[0].$sign[2].$sign[4].$sign[6].$sign[8].$sign[10].$sign[12].$sign[14];
                $force = isset($traceArr[2]) && $traceArr[2] == 'dev' ? 1 : 0;
                self::$force = $force;
                if( ($realSign == $traceArr[1] && abs(time() - $traceArr[0])<=self::$lifeCycle ) ||
                    $force == 1){
                    self::$switch = 1;
                }
            }
        }
        return self::$switch;
    }
}

