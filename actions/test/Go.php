<?php

/**
 * @author houpeng <houpeng01@baidu.com>
 */
class Action_Go extends Ap_Action_Abstract {

    protected $maps;
    protected $data;

    public function execute() {
        $request = $this->getRequest();
        $requestParams = $request->getRequest();
        
        // db连接
        $db = Box_Db_ConnMgr::getConn('boxlib_media');
        $db->query($sql);
        
        // meta获取
        $data  = array(
            array('nid'=>'dt_', 'field'=> ''),
            array('nid'=>'dt_', 'field'=> ''),
            array('nid'=>'dt_', 'field'=> ''),
        );
        $ret = Box_Feedmeta_Cache::mgetCache($data);
        
        // http请求
        $reqData = array(
            'service' => 'bdbox_ugc_bjh',
            'method' => 'post',
            'input' => $tempData,
            'header' => array(
                'pathinfo'      => '/builder/author/dynamic/create',
            ),
        );
        $ralObj = new Utils_Ral_SingleHttp($reqData);
        $response = $ralObj->request();
        
        // redis获取
        $config = array(
            'pid' => 'mbubsweb',
            'tk'  => 'mbubsweb',
            'app' => 'integral',
        );
        $redis = Mbd_Redis_Base::factory($config);
        
    }

}
