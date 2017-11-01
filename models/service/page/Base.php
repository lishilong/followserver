<?php
/**
 *
 * @brief base function, some function is repack of utils 
 * @name Service_Page_Base
 * @author weiyanjiang@baidu.com
 */

class Service_Page_Base {
    protected $baiduid; 
    protected $uk;
    protected $appid;
    public function __construct(){
    }

    /**
     *@param
     *@return
     */    
    public function __set($property,$value){
    	$this->$property = $value;
    }

    /**
     * @brief  请求logic的方法，返回logic的结果，如果未获取logic的结果抛出异常或者继续
     * @param  body 请求的消息体
     * @param  continueOnError 返回内容err_cdde !=0 时是否继续
     * @return logic 返回的json
     */
    protected function callLogicMethod($body, $continueOnError = true) {
        $res = Restapi_Util::sendMsg($body);
        if (isset($res['logic_result'])) {
            $logic_rs = json_decode($res['logic_result'], true);
            if ($logic_rs['err_code'] === 0) {
                return $logic_rs;
            }
        }
        if ($continueOnError === true) {
            return false;
        } else {
            throw new Exception("odp.logic_result_error ");
        }
    }
           
    /**
     * @brief  批量获取uid对应的uk列表
     * @param  number array bdUids
     * @param  string returnType "map" or "array"
     * @param  bool continueOnError
     * @return int array or false
     */
    protected function getUksByBdUids($bdUids, $returnType = "map", $continueOnError = false) {
        $validUids   = Restapi_Util::getValidUids($bdUids);
        if (false == $validUids
            || count($validUids) < 1) {
            if ($continueOnError === false) {
                throw new Exception('odp.param member param is error');
            } else {
                return false;
            }
        }

        $validUidUks = Restapi_Util::getUksAndLogin($validUids, $appid);
        if (false == $validiUidUks
            || count($validUidUks) < 1) {
            if ($continueOnError === false) {
                throw new Exception('odp.param member param is error');
            } else {
                return false;
            }
        }

        switch($returnType) {
            case "map":
                return $validUidUks;
            case "array":
                $validUks = array();
                foreach ($validUidUks as $uid => $uk) {
                    array_push($validUks, intval($uk));
                }
                return $validUks;
            default:
                throw new Exception("");
        }
        return $validUidUks;
    }

    /**
     * @param
     * @return
     */
    public function checkInt($params, $name) {
        $val = $params[$name];
        Bd_Log::addNotice($name, $val);
        if (!isset($val) || !is_numeric($val) || !is_int($val + 0) || $val < 0) { 
            throw new Exception("odp.param param $name error");
        }
        return intval($val);
    }

    /**
     * @param
     * @param
     * @return
     */
    public function checkIntWithDefault($params, $name, $default) {
        $val = $params[$name];
        Bd_Log::addNotice($name, $val);
        if (!empty($val) && !is_numeric($val)) { 
            throw new Exception("odp.param param $name error");
        }
        if (empty($val)) {
            $val = $default;
        }
        return intval($val);
    }

    /**
     * @param
     * @return
     */
    public function checkNull($params, $name) {
        Bd_Log::addNotice($name, $params[$name]);
        if (null === $params[$name]) {
            throw new Exception("odp.param param $name is null");
        }
    }

    /**
     * @param
     * @return
     */
    public function checkString($params, $name) {
        $val = $params[$name];
        Bd_Log::addNotice($name, $val);
        if (empty($val)) {
            throw new Exception("odp.param param $name is empty");
        }
        return $val;
    }

    /**
     * @brief check string param ,if is empty set default value
     * @param
     * @param
     * @param default default string
     * @return
     */
    public function checkStringWithDefault($params, $name, $default) {
        $val = $params[$name];
        Bd_Log::addNotice($name, $val);
        if (empty($val)) {
            $val = $default;
        }
        return $val;
    }
}
