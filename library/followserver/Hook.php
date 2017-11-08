<?php
/**
 * Created by PhpStorm.
 * User: chenhanchuan
 * Date: 17/11/8
 * Time: 上午11:07
 */

class Followserver_Hook extends Saf_Base_Hook {

    /**
     * 参数预处理
     */
    public function cgiAction() {

        $arrRequest = Saf_SmartMain::getCgi();
        $params = $arrRequest['request_param'];
        //客户端通讯的API 需要解析公共参数
        $paramsList       = json_encode($params);
        //日志记录
        Bd_Log::addNotice('request_param', $paramsList);

        Saf_SmartMain::unsetCgi(array('get','post','cookie','server','wiaui','query','request_param'));
        Saf_SmartMain::setCgi($params);
    }
}