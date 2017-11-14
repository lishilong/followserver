<?php
/**
 * Created by PhpStorm.
 * User: chenhanchuan
 * Date: 17/11/11
 * Time: 下午2:46
 */

class Utils_Image {

    /**
     *
     * @param $imgUrl
     * @return string
     */
    public static function feedHttpToHttps($imgUrl) {

        // 图片由于tn域名被劫持，临时增加替换策略
        $search = array('http://t10.baidu.com', 'http://t11.baidu.com', 'http://t12.baidu.com');
        $replace = array('http://f10.baidu.com', 'http://f11.baidu.com', 'http://f12.baidu.com');
        $imgUrl = str_replace($search, $replace, $imgUrl);

        return self::httpToHttps($imgUrl);
    }

    /**
     * @param $imgUrl
     * @return string
     */
    public static function httpToHttps($imgUrl) {

        if(!is_string($imgUrl) || empty($imgUrl)) {
            return '';
        }
        if(substr($imgUrl, 0, 7) == 'http://') {
            $img = 'https' . substr($imgUrl, 4);
            return $img;
        }
        return $imgUrl;
    }

    /**
     * @param $imgUrl
     * @return string
     */
    public static function httpsToHttp($imgUrl) {
        if(!is_string($imgUrl) || empty($imgUrl)) {
            return '';
        }
        if(substr($imgUrl, 0, 8) == 'https://') {
            $imgUrl = 'http' . substr($imgUrl, 5);
        }
        return $imgUrl;
    }


}

