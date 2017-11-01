<?php
/**
 * @filename: models/service/page/Follow.php
 * @desc
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:20:05
 * @last modify 2017-11-01 17:20:05
 */

class Service_Page_Follow extends Service_Page_Base{

    /**
     * @desc
     * @param 
     * @return 
     */
    public function execute($request){
        //$requestParams = $this->getRequest();
        //$adaptParams = $this->getAdaption();
        //$logInfo = $this->getLogInfo();
        $arrResult = array(
                'errno' => '-1',
                'timestamp'=>'0',
                'data' => array()
                );
        
        $time = time();

        // meta获取
        $nids = Service_Data_Nidpolicy::getNids();
        $metaItems = Service_Data_Feed::getFeedMetaByNids($nids);
        var_dump("weiyanjiang_meta_items",$metaItems);
/*
        $data  = array(
            array('nid'=>'news_122550092327743297', 'field'=> 'meta'),
            //array('nid'=>'news_322895089289744363', 'field'=> 'meta'),
            //array('nid'=>'news_18306228028894318076', 'field'=> 'meta'),
        );
        $ret = Box_Feedmeta_Base::mget($data);
        var_dump("weiyanjiang_ret:",$ret);

        //将cuid设置到cookie中
        if(!$_COOKIE['BAIDUCUID']){
            if($adaptParams['device_ua'] == 'android'){
                setcookie('BAIDUCUID',$requestParams['_UID'],$time+86400*30*1000,'/','.baidu.com');
            }else{
                setcookie('BAIDUCUID',Utils_Common::b64_encode($requestParams['uid']),$time+86400*30*1000,'/','.baidu.com');
            }
        }

        $data = isset($requestParams['data']) ? $requestParams['data'] : array();
        if(!empty($data)) {
            $requestParams['data_decode'] = json_decode(urldecode($data), true);
            $this->setRequest($requestParams);
        }

        //        $this->setLogInfo($logInfo);
  */
        echo json_encode($arrResult);
    }

    /**
     *数组转成json并且gzip压缩
     *@param $arr array参数
     *@return null
     */
    public static function jsonPackGzip($arr)
    {
        $str = "";
        if(is_array($arr) && !empty($arr)){
            $str = gzencode(json_encode($arr));
        }
        header("Content-Encoding: gzip");
        header("Vary: Accept-Encoding");
        header("Content-Length: " . strlen($str));
        header("Content-Type:application/x-www-form-urlencoded");
        echo $str;
    }
}
