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
    
    /**
     * V标
     * @var type 
     */
    private $flagV = array(
        'none' => '',
        'yellow' => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170927/1506489503937381.png',
        'golden' => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170927/1506489503967507.png',
        'blue' => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170927/1506489503819329.png',
    );
    
    /**
     * 映射关系
     * @var type 
     */
    private $flagMap = array(
        0 => 'none',
        1 => 'golden',
        2 => 'blue',
        3 => 'yellow',
    );

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
     * @desc 拼装RN模板1图模板（横图或者竖图）
     * @param string nid
     * @param string layout 默认为follow_horizontal_image RN横图1图模板 
     * @return 
     */
    public function buildTpl(&$meta){ 
        $templateData = array();
        $user = self::getUserInfoFromFeedMeta($meta);    
        if (false !== $user) {
            $templateData['user'] = $user;
        }
        return $templateData;
    }

    /**
     * @desc 通过nids和用户信息，拼装模板需要的原始信息
     * @param array nids
     * @return array/bool
     */
    public function getItemsFromNids($nids, $isNATemp = true, $uid = null, $cuid = null){ 
        $resItems = array();
        if (null === $uid && null === $cuid) {
            return false;
        }
        $nids = Service_Data_Nidpolicy::getNids();
        $metaItems = Service_Data_Feed::getFeedMetaByNids($nids);
        //var_dump("weiyangjiang________________metaItems",json_encode($metaItems[0]));
            
        $commentHandler = new Service_Data_Comment();
        $comments = $commentHandler->getCommentsCount(&$metaItems);
        
        $resItems = array();
        foreach ($metaItems as $meta) {
            $resItem = array();
            /*$resItem['nid']       = $meta['nid'];
            $resItem['title']     = $meta['title'];
            $resItem['imageurls'] = $meta['imageurls'];
            $resItem['source']    = $meta['displaytype_exinfo']['source'];
            $resItem['duration']  = isset($meta['displaytype_exinfo']['vid']) && isset($meta['displaytype_exinfo']['long']) ? $meta['displaytype_exinfo']['long'] : '';
            */
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
            
            $resItems[] = $resItem;
            
        }
        var_dump("tttttttttttttttttttttttttemp",json_encode($resItems));
        return $resItems;
    }

    
     /**
     * @desc 拼装用户信息
     * @param array meta
     * @return bool/array 获取成功返回array，失败返回false 
     */
    public function getUserInfoFromFeedMeta(&$meta, $isNATemp = true) {
        //获取用户头像，依次取https_avatar,author_img,http_avatar
        $photo = '';
        if(isset($meta['displaytype_exinfo']['https_avatar'])&&!empty($meta['displaytype_exinfo']['https_avatar'])){
            $photo = $meta['displaytype_exinfo']['https_avatar'];
        }elseif(isset($meta['author_img']['original']['url'])&&!empty($meta['author_img']['original']['url'])){
            $photo = $meta['author_img']['original']['url'];
        }elseif(isset($meta['displaytype_exinfo']['http_avatar'])&&!empty($meta['displaytype_exinfo']['http_avatar'])){
            $photo = $meta['displaytype_exinfo']['http_avatar'];
        }
        
        //获取资源发布者姓名 
        $sourceFrom = isset($meta['displaytype_exinfo']['source']) ? $meta['displaytype_exinfo']['source'] : '';
        if($sourceFrom=='ugc'||$sourceFrom=='ugc_baijiahao'){
            $source = isset($meta['displaytype_exinfo']['display_name']) ? $meta['displaytype_exinfo']['display_name'] : '';//资源发布者的姓名
        }else{
            $source = isset($meta['site']) ? $meta['site'] : '';//新闻来源
        }

        if(empty($photo) || empty($source)){
            return false;
        }
        //获取v标和对应的v标的图标url
        $vtype = isset($meta['displaytype_exinfo']['is_authenticated'])?(string)$meta['displaytype_exinfo']['is_authenticated']:'';
        if ('ugc' == $sourceFrom || 'ugc_baijihao' == $sourceFrom) {
            $vtype = '';
        }
        $vurl = isset($this->flagMap[intval($vtype)]) ? $this->flagV[$this->flagMap[intval($vtype)]] : '';

        $createTime = isset($meta['ts']) ? $meta['ts'] : '';

        if ($isNATemp) {
            $user = array(
                'photo' => $photo,
                'name'  => array(
                    'text'        =>$source,
                    'create_time' => $createTime,
                ),
                'vtype' => $vtype,
                'v_url' => $vurl,
                'cmd'   => $userCmd,
            );
        } else {
            $user = array(
                'photo' => $photo,        
                'name_text' => $source,        
                'update_time' => $createTime,        
                'cmd' => array(
                        "mode" => 2,
                        "url"  => $userCmd,
                    ),        
                'vtype' => $vtype,        
                'v_url' => $vurl,        
            );
        }
        return $user;
    }
}
