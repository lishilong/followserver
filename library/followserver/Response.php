<?php
/**
 * @filename: library/followserver/Response.php
 * @desc 封装restapi response
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:14:05
 * @last modify 2017-11-01 17:14:05
 */

class Followserver_Response {
    
    private static $arrFieldsNeedDealBeforeSend = array (    // 由于nodejs不能处理64位的整形数据，故在响应前，先将他们做强制类型转换
        'timer_id' => 'strval',
        'msg_id' => 'strval',
        'channel_id' => 'strval',
        'job_id' => 'strval',
    );
    protected static $outputs = array();
    protected static $exception = null;
    protected static $error = null;
    
    /**
     * 
     * 设置待返回结果
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value = null) {
        /*if (isset(self::$arrFieldsNeedDealBeforeSend[$key])) {
            $funName = self::$arrFieldsNeedDealBeforeSend[$key];
            $value = $funName($value);
        }*/
        self::$outputs[$key] = $value;
    }
    
    /**
     * 
     * 组织响应消息体
     * @return array
     */
    private static function getResult() {
        if (self::$exception || self::$error) {
            $result = self::$outputs;
        }
        else {
            if (!empty(self::$outputs)) {
                $result['request_id'] = Followserver_Request::getLogid();
                $result['error_code'] = self::$outputs['error_code'];

                //将error_code提到response_params外面
                unset(self::$outputs['error_code']);
                if (0 != $result['error_code']) {
                    $result['error_msg'] = self::$outputs['error_msg'];
                } else if(0 < count(self::$outputs)) {
                    $result['response_params'] = self::$outputs;
                }
            }
            else {
                $result = array('request_id' => Followserver_Request::getLogid());
            }
        }
        return $result;
    }
    
    /**
     * 
     * 组织响应信息
     * @return string
     */
    public static function formatResponse() {
        $result = self::getResult();
        return @json_encode($result);
    }
    
    /**
     * 
     * 记录处理中的异常信息
     * @param mixed $ex
     */
    public static function setException($ex) {
        self::$exception = $ex;
    }

    /**
     * 
     * 捕获异常
     * @param object $ex      异常错误信息
     */
    public function exceptionHandler($ex) {
        $exceptionMsg = $ex->getMessage();
        $pos = strpos($exceptionMsg, ' ');
        $errCode = '';
        if (0 < $pos) {
            $errCode = substr($exceptionMsg, 0, $pos);
            $errMsg = substr($exceptionMsg, $pos + 1);
        }
        
        $fatalMsg = sprintf('Caught exception, errcode:%s, trace: %s', $errCode, $ex->__toString());
        Bd_Log::fatal($fatalMsg);
        Followserver_Response::setException($ex);
        self::abend($errCode, $errMsg);
    }
    /**
     * 
     * 对error或exception错误的具体处理
     * @param string $errCode       错误码
     * @param string $errMsg        具体错误信息
     */
    private function abend($errCode, $errMsg) {
        if (!isset(Restapi_Exception::$arr_exception_http_ret_map[$errCode])) {
            $errCode = 'odp.internal';
        }
        
        $mapErrorCode = Restapi_Exception::$arr_exception_http_ret_map[$errCode]['error_code'];
        $mapErrorMsg  = Restapi_Exception::$arr_exception_http_ret_map[$errCode]['error_msg'];
        if ($errCode === 'odp.param') {
            $mapErrorMsg .= ', ' . $errMsg;
        }
        
        Followserver_Response::set('request_id', Followserver_Request::getLogid());
        Followserver_Response::set('error_code', $mapErrorCode);
        Followserver_Response::set('error_msg', $mapErrorMsg);
        //Followserver_Response::send();
        Bd_Log::addNotice('error_code' , $mapErrorCode);
        Bd_Log::addNotice('error_msg' , $mapErrorMsg);

        $detailMsg = sprintf('Caught exception, errcode:%s, detail: %s', $errCode, $errMsg);
        Bd_Log::addNotice('error detail',$detailMsg);
    }

    public function __construct(){
    }
    
}
