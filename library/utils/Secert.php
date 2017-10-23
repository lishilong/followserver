<?php
/**
 * 
 * @author duwei04
 */
class Utils_Secert{
    /**
     * 生成http sign
     * @param array $params 待签名的数据
     * @param string $secret_key  密钥
     * @param string $signFlagName 签名字段名称
     * @return string
     */
    public static function genHttpSign($params,$secret_key,$signFlagName="sign"){
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            if ($signFlagName != $k) {
                $str .= "{$k}={$v}";
            }
        }
        $str .= $secret_key;
        return md5($str);
    }
    
}