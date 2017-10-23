<?php
class Resbox_Request{
    private static $arrtibute=array();
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
}