<?php
/**
 * @filename: models/service/data/Like.php
 * @desc 获取点赞相关数据，详见：http://wiki.baidu.com/pages/viewpage.action?pageId=317138707#id-服务端的API-12.查询uid/cuid点赞/点踩信息及资源的赞踩数
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-06 11:48:26
 * @last modify 2017-11-06 11:48:26
 */

class Service_Data_Like extends Service_Data_Base
{
    const AK = 'feed';
    const SK = 'ed9743bf20b9fe766bf46ec4130b8b6a';

    private $likeSource=array(
            'ugcsimple'=>'ugc',
            'ugcbjh'=>'ugc_baijiahao',
            'star'=>'star',
            );
    /**
     * 继承父方法
     */
    public function execute()
    {

    }
    /**
     * @brief 批量获取nids的点赞相关数据入口函数 
     * @param array ids nid列表
     * @param type
     * @return boolean|array
     * @sample
     {
        "feed":{
             "news_678741571145693057":{
             "status":"unset",
             "like":"0",
             },
             "news_17991983688588921690":{
             "status":"unset",
             "like":"0",
             }
        }
     }
     */
    public function getLikeData($nids, $uid, $type='feed')
    {
        $nids = array_unique($nids);
        foreach ($nids as $nid) {
            $ids[] = $type . '_' . 'news_' . $nid;
        }
        $ret = $this->getLikeInfo($ids,$uid);
        return $ret;

    }
    /**
     * @brief like
     * @param nid
     * @param type
     * @return boolean|array
     */
    public function getLikeInfo($ids,$uid,$page='feedlist')
    {
        $result = array();
        $likeIdsChunk = array_chunk($ids, 20);
        $likeData = array();
        foreach($likeIdsChunk as $key => $val){
            $likeDataChunk = $this->getList($val,$uid);
            $likeData = array_merge_recursive($likeData, is_array($likeDataChunk) ? $likeDataChunk : array());
        }
        $result = $likeData;
        /*foreach($ids as $key => $val){
        //val:feed_news_${nid}
        $idArr = explode('_',$val);
        //type:feed
        $type = array_shift($idArr);
        //id:news_${nid}
        $id = implode('_',$idArr);
        if(isset($likeData[$type][$id])){
        $item = $likeData[$type][$id];
        $result[$val]['status'] = (isset($item['status'])&&$item['status']=='like')?'1':'0';
        $result[$val]['count'] = isset($item['like'])?strval($item['like']):'0';
        }else{
        $result[$val]['status'] = '0';
        $result[$val]['count'] = '0';
        }
        }*/
        return $result;
    }
    /**
     * @brief like
     * @param string nid :feed_news_{$nid}
     * @param number type 1:获取like和degrade计数器 ； 2:仅获取like计数器 ； 3:仅获取degrade计数器；
     * @return boolean|array
     */
    public function getList($ids,$uid,$get_type=2)
    {
        $ids_str = implode(',',$ids);
        $params = array(
                'ids' => $ids_str,
                'uid'=>'',
                'cuid'=>'',
                'get_type'=>$get_type,
                'sfrom'=>'',
                'source'=>'feedlist',
                'ak'=>self::AK,
                'time'=>time(),
                );
        //是否登录请求不同的接口
        if($uid>0){
            $params['uid'] = $uid;
        }else{
            /*$requestParams = Param_ObserverMain::getRequest();
              if(!empty($requestParams['uid'])){
              $params['cuid'] = $requestParams['uid'];
              }elseif(!empty($requestParams['cookie']['baiducuid'])){
              $params['cuid'] = Box_Utils::b64_decode($requestParams['cookie']['baiducuid']);
              }else{
              $params['cuid']='';
              }*/
        }
        $params['sign'] = Box_Util_Secert::genHttpSign($params,self::SK);
        $ralParams = array(
                'service' => 'resbox',
                'method' => 'get',
                'input' => array(),
                'header' => array(
                    'pathinfo' => '/resbox/like/common/list',
                    'querystring' => http_build_query($params, '&'),
                    ),
                );
        $ralService = new Utils_Ral_SingleHttp($ralParams);
        if (false === ($ret = $ralService->request())) {
            Box_Log_Txt::setWarning('45013011', 'get like list request fail, resource:'. json_encode($ralParams));
            return false;
        }
        $ret = json_decode($ret, true);
        if(!isset($ret['errno']) || $ret['errno'] != 0){
            Box_Log_Txt::setWarning('45013012', 'get like list request fail, resource' . json_encode($ralParams).',response:'. json_encode($ret));
            return false;
        }
        if(!isset($ret['data']) || empty($ret['data'])){
            Box_Log_Txt::setWarning('45013013', 'get user like list return error,resource:'. json_encode($ralParams).', response:'. json_encode($ret));
            return false;
        }
        $items = $ret['data'];
        return $items;
    }
    /**
     * @param array $otherTokenMsg  array('nid' = xxx)
     * @return string
     */
    /* private function getBdstoken($nid,$uid){
       $arrSrc = array();
       $strKey = 'feed123bdstoken';
       $requestParams = Param_ObserverMain::getRequest();
       if($uid>0){
       $arrSrc['uid'] = $uid;
       }elseif(!empty($requestParams['cuid'])){
       $arrSrc['cuid'] = $requestParams['cuid'] ;
       }elseif(!empty($requestParams['cookie']['baiduid'])){
       $arrSrc['baiduid'] = $requestParams['cookie']['baiduid'];
       }
       $strToken = '';
       if(!empty($arrSrc)) {
       $arrSrc['preTime'] = time();
       $arrSrc['nid'] = $nid;
       $strToken = Bd_Crypt_Rc4::rc4(json_encode($arrSrc), 'ENCODE', $strKey);
       }
       return $this->base_encode($strToken);
       }*/
    /**
     * 替换特殊字符   防止urldecode转码
     * @param $str
     * @return mixed
     */
    private function base_encode($str) {
        $src  = array("/","+","=");
        $dist = array("_a","_b","_c");
        $old  = base64_encode($str);
        $new  = str_replace($src,$dist,$old);
        return $new;
    }
}
