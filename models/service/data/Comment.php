<?php
/**
 * @filename: models/service/data/Comment.php
 * @desc 获取评论数据，详见：http://wiki.baidu.com/pages/viewpage.action?pageId=207968303
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-06 11:48:26
 * @last modify 2017-11-06 11:48:26
 */

class Service_Data_Comment extends Service_Data_Base
{

    const USER_TYPE_STAR = 'star';
    const USER_TYPE_BJH = 'ugcbjh';
    const USER_TYPE_UGC = 'ugcsimple';

    protected $comment_key = array(
            self::USER_TYPE_STAR => array(
                'app_id' => 127,
                'app_key' => '85d2632249',
                ),
            self::USER_TYPE_BJH => array(
                'app_id' => 131,
                'app_key' => 'fjlaur9889',
                ),
            self::USER_TYPE_UGC => array(
                'app_id' => 130,
                'app_key' => 'vdjop73djl',
                ),
            );

    /**
     * @desc
     * @param 
     * @return 
     */
    public function __construct($resource){
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
     * @desc
     * @param 
     * @return 
     */
    public function getCommentsCount(&$metas)
    {
        $results = array();
        foreach ($metas as $meta) {
            $keys[] = self::getCommentKeyByMeta($meta);
        }
        if (0 < count($keys)) {
            $results = self::getCommentNumsByIds($keys);
        }
        return $results;
    }

    /**
     * @desc
     * @param 
     * @return 
     */
    public static function getCommentKeyByMeta(&$meta)
    {
        $key = '';
        if ($meta['displaytype_exinfo']['source'] == 'ugc') {
            $type = self::USER_TYPE_UGC;
        } else if ($meta['displaytype_exinfo']['source'] == 'ugc_baijiahao') {
            $type = self::USER_TYPE_BJH;
        } else if ($meta['displaytype_exinfo']['source'] == 'ugc_star') {
            $type = self::USER_TYPE_STAR;
        } else {
            continue;
        }

        $isVideo = false;
        if (isset($meta['displaytype_exinfo']['vid'])) {
            $isVideo = true;
        }
        if ($isVideo) {
            $key = $type . '_sv_' . $meta['displaytype_exinfo']['vid'];
        } else {
            $key = $type . '_dt_' . $meta['nid'];
        }
        $keys[] = $key;
        return $key;
    }

    /**
     * @desc 批量获取评论数
     * @param array|string $ids
     * @return array
     */
    public function getCommentNumsByIds($ids)
    {
        $result = array();
        $queryString = array(
                'keys' => implode(',', $ids),
                'appid' => 127,
                );
        $sign = $this->genSign($queryString, $appKey = '85d2632249');
        $queryString['sign'] = $sign;
        $pathInfo = '/api/comment/v1/comment/ktcount';
        $req = Box_Util_RalRequest::simpleHTTP('unicomment', 'get', $pathInfo, $queryString);
        $resp = Box_Util_RalClient::callSync($req);
        $obj = json_decode($resp, true);
        if (empty($obj) || $obj['errno'] != 0) {
            Mbd_Log_Txt::setDebug('getCommentNumsByIds', 'webpage');
        } else {
            $result = !empty($obj['ret']) ? $obj['ret'] : array();
        }
        return $result;
    }

    /**
     * 统一评论模块签名
     * @param  array $arrContent 被加密的数据
     * @param  string $appKey 加密用appKey，新接入时按appid分配
     * @return string
     */
    public function genSign($arrContent, $appKey)
    {
        ksort($arrContent);
        $arr = array();
        foreach ($arrContent as $key => $value) {
            $arr[] = "{$key}={$value}";
        }
        $gather = implode('&', $arr);
        $gather .= '&';
        $gather .= $appKey;
        $sign = md5($gather);
        return $sign;
    }
}
