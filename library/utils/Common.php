<?php
/**
 * Created by PhpStorm.
 * User: v_lishilong
 * Date: 2017/11/9
 * Time: 14:06
 */
class Utils_Common{

    /**
     * 指定键数据从源数组中获取值
     * @param $arrNeedKeys
     * @param $data
     * @return array
     */
    public static function buildNeedInputs($arrNeedKeys, $data) {

        $inputs = array();
        foreach ($arrNeedKeys as $key) {
            self::setIfIsSet($inputs, $data, $key);
        }
        return $inputs;
    }

    /**
     * 设定数组如果存在值
     * @param $toArr
     * @param $fromArr
     * @param $toKey
     * @param null $fromKey
     */
    public static function setIfIsSet(&$toArr, $fromArr, $toKey, $fromKey = null) {
        if (!$fromKey) {
            $fromKey = $toKey;
        }
        if (isset($fromArr[$fromKey])) {
            $toArr[$toKey] = $fromArr[$fromKey];
        }
    }
}