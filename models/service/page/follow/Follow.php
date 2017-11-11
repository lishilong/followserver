<?php
/**
 * @filename: models/service/page/common/Follow.php
 * @desc
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:20:05
 * @last modify 2017-11-01 17:20:05
 */

class Service_Page_Follow_Follow extends Service_Page_Follow_Base {

    /**
     * @return array
     */
    protected function run() {

        $time = time();

        // meta获取
        $nids = Service_Data_Nidpolicy::getNids($this->requests);
        $metaItems = Service_Data_Feed::getFeedMetaByNids($nids);
        //var_dump("weiyanjiang_request",json_encode($request));
        // 获取点赞数
        $uid = 621388556;
        $likeHandler = new Service_Data_Like();
        $ret = $likeHandler->getLikeData($nids, $uid, 'feed');

        var_dump("weiyanjiang_likeData",json_encode($ret));

        $threadIds = array();
        $commentHandler = new Service_Data_Comment();
        foreach ($metaItems as $meta) {
            //    var_dump("weiyangjiang________________meta",$meta['meta']);
            $metaInfo = json_decode($meta['meta'], true);
            if (isset($metaInfo['thread_id']) && 0 < strlen($metaInfo['thread_id'])) {
                $threadIds[] = $metaInfo['thread_id'];
            }
        }

        $ret = $commentHandler->commentFactory->mGetCountByThreadId($threadIds);
        var_dump("weiyanjiang_ret",$ret);

        $data = array();
        return $data;
    }
}
