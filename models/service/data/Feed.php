<?php
/**
 * @filename: models/service/data/Feed.php
 * @desc 根据nid获取meta数据
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:14:48
 * @last modify 2017-11-01 17:14:48
 */

class Service_Data_Feed extends Service_Data_Base{

    public function execute(){}

    /**
     * @desc 根据nid列表获取对应的meta数据 
     * @param 
     * @return 
     */
    public function getFeedMetaByNids($nids){
        $resItems = array();
        if (!is_array($nids) || 0 === count($nids)) {
            return $resItems;
        }
        $inputData = array();
        $nids = array_unique($nids);
        foreach ($nids as $nid) {
            $inputData[] = array(
                    'nid' => 'news_' . $nid,
                    'field' => 'meta'); 
        }

        $metaItems = Box_Feedmeta_Base::mget($inputData);
        
        foreach ($metaItems as $meta){
            $values = array_values($meta);
            $metaInfo = json_decode($values[0], true);
            if (is_array($metaInfo) && 0 < count($metaInfo)) {
                array_push($resItems, $metaInfo);
            }

        }
        return $resItems;
    }
}
