<?php

/**
 * 缓存key生成
 * @author baijianmin
 * @version $Id: Ckey.php 276063 2015-12-07 02:43:46Z baijianmin $
 */
class Utils_Ckey {

    const CACHE_PREFIX = 'resbox_app_';
    const CARD_GOODS_CITYCODE = 'card_goods_%d';

    /**
     * 获取缓存所使用的key
     * @param string $tpl   模版
     * @param array $args   参数
     * @return string
     */
    public static function get($tpl, $args) {
        $tpl = self::CACHE_PREFIX . $tpl;
        array_unshift($args, $tpl);
        $ckey = call_user_func_array('sprintf', $args);
        return $ckey;
    }

}
