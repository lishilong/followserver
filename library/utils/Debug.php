<?php
/**
 * Created by PhpStorm.
 * User: v_lishilong
 * Date: 2017/11/9
 * Time: 14:29
 */
class Utils_Debug
{
    /**
     * @var null
     */
    protected static $isDebug = null;
    /**
     * @var null
     */
    protected static $debugData = null;

    /**
     * 是否是debug模式
     * @return bool|null
     */
    public static function isDebug() {
        if (is_null(self::$isDebug)) {
            $debug = Bd_Conf::getAppConf('common/debug/switch');
            self::$isDebug = ($debug == 1 ? true : false);
        }
        return self::$isDebug;
    }

    /**
     * 添加debug数据
     * @param $key
     * @param $value
     */
    public static function addDebugData($key, $value) {

        if (self::isDebug()) {
            self::$debugData[$key] = $value;
        }
    }

    /**
     * 获取debug数据
     * @return null
     */
    public static function getDebugData() {
        return self::$debugData;
    }
}