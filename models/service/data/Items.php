<?php
/**
 * @filename: models/service/data/Items.php
 * @desc 拼装模板数据 
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-07 15:26:47
 * @last modify 2017-11-07 15:26:47
 */

class Service_Data_Items extends Service_Data_Base
{

    private $meta;
    const RECOMMENT_TEMPLATE_ID = 29;//id为29表示为推荐模板，复用之前FEED GR该字段含义    
    /**
     * @desc
     * @param 
     * @return 
     */
    public function __construct(){
    }

    /**
     * @desc
     * @param 
     * @return 
     */
    public function execute()
    {
    }
 
    /**
     * @desc 判断模板是否为推荐模板
     * @param 
     * @return 
     */
    public function isRecommentTemplate(&$feedListItem){
        $ids = $feedListItem['display_strategy']['templates']["id"];
        if (1 == count($ids) && in_array(self::RECOMMENT_TEMPLATE_ID,$ids)) {
            return true;
        }
        return false;
    }
    
    /**
     * @desc 通过nids和用户信息，拼装模板需要的原始信息
     * @param array nids
     * @return array/bool
     */
    public function getItems($uid = null, $cuid = null){ 
        $resItems = array();
        if (null === $uid && null === $cuid) {
            return false;
        }
        $nids = array();
        $recommentMeta = array();
        $feedList = Service_Data_Nidpolicy::getFeedList();
        
        $countFeedList = count($feedList);
        for ($i = 0; $i < $countFeedList; $i++) {
            if (self::isRecommentTemplate($feedList[$i])) {
                $recommentMeta[] = array(
                        "pos" => $i,
                        "data" => $feedList[$i],
                        ); 
            } else {
                $nids[] = $feedList[$i]["id"];
            }
        }
        
        $metaItems = Service_Data_Feed::getFeedMetaByNids($nids);
        $commentHandler = new Service_Data_Comment();
        $comments = $commentHandler->getCommentsCount($metaItems);
        
        $resItems = array();
        foreach ($metaItems as $meta) {
            $resItem = array();
            $resItem['nid'] = $meta['nid'];
            $resItem['meta'] = $meta;
            $likeHandler = new Service_Data_Like();
            $resLikes = $likeHandler->getLikeData($nids, $uid, 'feed');
        
            //点赞相关
            if (isset($resLikes['feed']['news_' . $meta['nid']])) {
                $resItem['like']['count'] = $resLikes['feed']['news_' . $meta['nid']]['like'];
                $resItem['like']['type']  = $resLikes['feed']['news_' . $meta['nid']]['status'] === 'like' ? "1" : "0";//type为“1”表示已经点赞
            }
            
            //评论相关
            $commentKey = Service_Data_Comment::getCommentKeyByMeta($meta);
            $resItem['comment'] = array(
                "count" => isset($comments[$commentKey])? ''.$comments[$commentKey]:"",
            );

            $resItem['type'] = 1; //todo 测试 暂设
            
            $resItems[] = $resItem;
            
        }
        //完成拼接，把推荐账号meta拼装到resItems的指定位置
        foreach ($recommentMeta as $meta) {
            $item = array();
            $item['nid'] = $meta['data']['id'];
            $item['meta'] = $meta;
            $item['type'] = 0;
            if ($meta['pos'] < count($resItems)) {
                array_splice($resItems, $meta['pos'], 0, array($item));
            } else {
                $resItems[] = $item;
            }
        }
        return $resItems;
    }
}
