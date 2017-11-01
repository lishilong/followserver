<?php
/*
 * Service_Data_Common_Feed
 *
 * 通用推荐 区别GR 目前手百8.1 widget使用
 * c++模块 nshead+json
 *
 * @author Liaoweida [Liaoweida@baidu.com]
 * @version 1.0
 */

class Service_Data_Feed extends Service_Data_Base{

    public function execute(){}
    
    /**
     * @brief 根据nid列表获取对应的meta数据 
     * @param array $nids
     * @return array
     */
    public function getFeedMetaByNids($nids){
        $metaItems = array();
        if (!is_array($nids) || 0 === count($nids)) {
            return $metaItems;
        }
        $inputData = array();
        $nids = array_unique($nids);
        foreach ($nids as $nid) {
            $inputData[] = array(
                    'nid' => 'news_' . $nid,
                    'field' => 'meta'); 
        }
       
        $metaItems = Box_Feedmeta_Base::mget($inputData);
        return $metaItems;
    }

    
    /**
     * @param $count
     * @return array
     */
    /*public function _feedList($count=4){
        $resource = $this->getResource();
        $requestParams = $this->getRequest();
        $adaptParams = $this->getAdaption();
        $arrResult = array();
        $ralObj = new Utils_Ral_Nshead('bdbox_grfeed');
        $inputArray = array(
            'product'       => 0,
            'loc_city'      => '',
            'loc_district'  => '',
            'loc_point'     => '',
            'loc_province'  => '',
            'refresh_type'  => 0,
            'command'       => 'widget',//个性化推荐接口
            'cuid'          => $requestParams['uid'],
            'reqnums'       => $count,//取4条
            'uid'           => isset($requestParams['puid']) ? strval($requestParams['puid']) : '',
            'app_version'   => $adaptParams['bd_version'],//app version
            'ua'            => isset($requestParams['ua']) ? $requestParams['ua'] : '',
            'ut'            => isset($requestParams['ut']) ? $requestParams['ut'] : '',
            'network'       => isset($requestParams['network']) ? $requestParams['network'] : '',
            'osname'        => isset($requestParams['osname']) ? $requestParams['osname'] : '',
            'osbranch'      => isset($requestParams['osbranch']) ? $requestParams['osbranch'] : '',
            'ip'            => isset($requestParams['cip']) ? $requestParams['cip'] : '',
            'baiduid' => empty($requestParams['cookie']['baiduid']) ? '' : $requestParams['cookie']['baiduid'],
        );
        $reqData = array(
            array(
                'service'       => 'bdbox_grfeed',
                'input'         => json_encode($inputArray),
            ),
        );

        if(null !== $ralObj){
            $response = $ralObj->request($reqData);
            //ral请求成功
            if($ralObj->getErrno() == 0 ){
                $arrResult['errno'] = '0';
                $grResponse = json_decode($response[0],true);
                if (null === $grResponse) {
                    $arrResult['error'] = is_string($response[0]) ? $response[0] : '';
                }
                $arrResult['data'] = $grResponse;
            }else{
                $arrResult['errno'] = $ralObj->getErrno();
                $arrResult['error'] = $ralObj->getError();
            }
        }else{
            $arrResult['errno'] = -1;
            $arrResult['error'] = 'ral_ral_nshead init failure';
        }
        return $arrResult;
    }*/
}
