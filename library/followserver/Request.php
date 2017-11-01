<?php
class Followserver_Request{
    protected static $requestId = 0;
    private static $arrtibute=array();

    public function __construct(){
    }

    /**
     * 
     * @param string $key
     * @param mixed $val
     */
    public static function setAttribute($key,$val){
        self::$arrtibute[$key]=$val;
    } 
    /**
     * 读取一个属性
     * @param string $key
     * @param mixed $default
     * @return mixd
     */
    public static function getAttribute($key,$default=null){
        $key_arr=explode("/", trim($key,"/"));
        $attr=self::$arrtibute;
        foreach ($key_arr as $k){
            if(array_key_exists($k, $attr)){
                $attr=$attr[$k];
            }else{
                return $default;
            }
        }
        return $attr;
    }
    /**
     * 
     * 初始化请求信息
     * @param array $arrConf
     */
    public static function init($arrConf = array()) {
        self::$requestId = self::genRequestId();
    }

    /**
     * 
     * 获得该次请求的唯一id
     * @return int
     */
    public static function getLogid() {
        return self::$requestId;
    }

    /**
     * 
     * 生成该次请求的唯一id
     * @return int
     */
    private static function genRequestId() {
        if (isset($_SERVER['HTTP_CLIENTAPPID'])) {
            return intval($_SERVER['HTTP_CLIENTAPPID']);
        }

        $randval = mt_rand() + mt_rand() + 1;
        $requestId = $randval & 4294967295;
        return $requestId;
    }

}
