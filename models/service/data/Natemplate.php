<?php
/**
 * @filename: models/service/data/Template.php
 * @desc 拼装模板数据 
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-07 15:26:47
 * @last modify 2017-11-07 15:26:47
 */

class Service_Data_Natemplate extends Service_Data_Base
{
    const UGC_LAND_URL = 'https://mbd.baidu.com/webpage?type=user&action=dynamic&context=%s';
    const STAR_LAND_URL = 'https://mbd.baidu.com/webpage?type=celebrity&action=dynamic&context=%s';
    const FEED_VIDEO_URL = 'https://sv.baidu.com/videoui/page/videoland';

    const UGC_LAND_HYBRID = 'baiduboxapp://v1/easybrowse/hybrid?upgrade=1&type=hybrid&tplpath=profile&tpl_id=dynamic.html&context=%s&style=%s&newbrowser=1&slog=%s';
    const STAR_LAND_HYBRID = 'baiduboxapp://v1/easybrowse/hybrid?upgrade=1&type=hybrid&tplpath=profile&tpl_id=dynamic.html&context=%s&style=%s&newbrowser=1&slog=%s';

    const HOME_LAND_HYBRID = 'baiduboxapp://v1/easybrowse/hybrid?upgrade=1&type=hybrid&tplpath=profile&tpl_id=profile.html&context=%s&style=%s&newbrowser=1&slog=%s';

    const FEED_VIDEO_LAND = 'baiduboxapp://v1/easybrowse/open?newbrowser=1&append=1&upgrade=1&type=video&url=%s&toolbaricons=%s&menumode=%s&slog=%s';

    private $_tnToken = null;

    private $_item = null;
    private $_meta = null;
    private $_like = null;
    private $_comment = null;
    private $_bigImage = '';
    private $_imageItems = array();
    private $_tpl = null;
    private $_sourceFrom = null;
    private $_source = null;
    private $_likeType = null;
    private $_commentCmd = null; //详情页评论schema
    private $_shareUrl = null;
    private $_likeExt = null;

    private $_toolBarIcons = array(
        'toolids'=>array(// 1、2、3 和4不同时出现，会双bar
            '1',//评论（显示评论数）
            '2',//收藏
            '3',//分享
            //'4',//评论输入框
        ),
    );
    private $_menuMode = '2';//可选 1:纯H5页面菜单。收藏，下载，刷新，分享，设置，意见反馈 2:feed 菜单。 刷新，复制，意见反馈，iOS 多一个夜间模式 3:NA 菜单。收藏，下载，设置，意见反馈

//    private $_slog = array(
//        'from'   => 'feed',//业务标识
//        'type'   => 'time',//时长
//        'page'   => '',//页面标识
//        'source' => 'index',
//        'ext'    => '',
//    );

    // V标图片
    private $_flagV = array(
        0 => '', //none
        1 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460792824.png', //golden
        2 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460970589.png', //blue
        3 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460903468.png', //yellow
    );

    /**
     * Service_Data_Natemplate constructor.
     * @param $requests
     */
    public function __construct($requests) {
        $this->_tnToken = Bd_Conf::getAppConf('common/tn_token');
    }


    /**
     * 构建模板
     * @param $item
     * @return bool|null
     */
    public function buildTemplate($item) {

        $this->_item = $item;
        $this->_meta = &$this->_item['meta'];
        $this->_like = &$this->_item['like'];
        $this->_comment = &$this->_item['comment'];

        $sucFlag = false;
        if ($this->_item['type'] == 1 && $this->_buildNormalTemplate()) {
            $sucFlag = true;
        } elseif ($this->_buildRecommendTemplate()) {
            $sucFlag = true;
        }
        $tpl = $this->_tpl;
        $this->_clean();
        if (!$sucFlag) {
            return false;
        }
        return $tpl;
    }

    /**
     * 清除临时字段
     */
    private function _clean() {
        $this->_item = null;
        $this->_meta = null;
        $this->_like = null;
        $this->_comment = null;
        $this->_bigImage = '';
        $this->_imageItems = array();
        $this->_tpl = null;
        $this->_sourceFrom = null;
        $this->_source = null;
        $this->_likeType = null;
        $this->_commentCmd = null;
        $this->_shareUrl = null;
        $this->_likeExt = null;
    }

    /**
     * 构建横滑关注模板
     */
    private function _buildRecommendTemplate() {

    }

    /**
     * 构建通用模板
     * @return bool
     */
    private function _buildNormalTemplate() {

        /**
        $this->_tpl = array(
            'layout' => 'star_text',
            'data'   => array(
                'user' => array(            //用户信息
                    'photo' => '',          //头像
                    'name'  => array(       //用户名
                        'text'        => '',      //明星姓名
                        'create_time' => '',      //资源发布时间
                    ),
                    'cmd'   => '',
                    'vtype' => '',              //V标，0 无，1 橙V，2 蓝V
                    'v_url' => '',
                ),
                'text0'      => '',                  //文本内容, 仅纯文本使用
                'image'      => '',                  //仅大图模板使用
                'title'      => '',                  //文本内容不使用
                'title_rich' => array(),             //仅多图模板使用
                'items'      => array(),             //仅多图模板使用
                'source'     => '',                  //资源来源，只有来自微博的资源，才显示来源
                'cmd'        => '',
                'comoment_num'  => '',              //阅读数
                'bar'       => array(
                    'like' => array(
                        'count' => '',              //点赞数
                        'type'  => '',              //0 未点赞，1 已点赞
                        'ext'   => array(),         //调用点赞接口时透传
                    ),
                    'comment' => array(
                        'count' => '',              //评论数
                        'cmd'   => '',
                    ),
                    'share'   => array(
                        'url'   => '',
                        'title' => '',
                        'image' => '',
                    ),
                ),
                'duration'  => '',                  //仅大图模板使用
                'type'      => '',                  //仅大图模板使用
            ),
        );
        **/

        $this->_sourceFrom = $this->_meta['displaytype_exinfo']['source'] ? $this->_meta['displaytype_exinfo']['source'] : '';
        if ($this->_sourceFrom == 'ugc' || $this->_sourceFrom == 'ugc_baijiahao') {
            $this->_source = $this->_meta['displaytype_exinfo']['display_name'] ? $this->_meta['displaytype_exinfo']['display_name'] : '';
        } else {
            $this->_source = $this->_meta['site'] ? $this->_meta['site'] : '';
        }

        $this->_setBigImage();
        $this->_setImageItems();

        //判断应该使用哪种模板
        if ($this->_meta['type'] == 'text') {
            //图文模板
            if (!$this->_buildNewsTpl()) {
                return false;
            }
        } elseif ($this->_meta['type'] == 'videolive') {
            //视频模板
            if (!$this->_buildVideoTpl()) {
                return false;
            };
        } else {
            //不支持
            Bd_Log::warning('not support type [' . $this->_meta['type'] . ']');
            return false;
        }
        
        $vType = isset($this->_meta['displaytype_exinfo']['is_authenticated']) ? strval($this->_meta['displaytype_exinfo']['is_authenticated']) : '';

        $this->_likeType = 'star';
        switch ($this->_sourceFrom) {
            case 'star':
                $this->_likeType = 'star';
                break;
            case 'ugc':
                $vType = '';
                $this->_likeType = 'ugcsimple';
                break;
            case 'ugc_baijiahao':
                $vType = '';
                $this->_likeType = 'ugcbjh';
                break;
            default:
                break;
        }

        if (!$this->_setUserData($vType)) {
            return false;
        }

        $this->_setBar();

        //阅读数
        $this->_tpl['data']['comment_num'] = isset($this->_item['read_count']) ? $this->_item['read_count'] : '';

        return true;
    }

    /**
     * 设置底bar
     */
    private function _setBar() {

        $this->_likeExt['type'] = $this->_likeType;
        $this->_tpl['data']['bar'] = array(
            'like' => array(
                'count' => isset($this->_like['count']) ? $this->_like['count'] : '',
                'type'  => isset($this->_like['type']) ? $this->_like['type'] : '',
                'ext'   => json_encode($this->_likeExt),
            ),
            'comment' => array(
                'count' => isset($this->_comment['count']) ? $this->_comment['count'] : '',
                'cmd'   => $this->_commentCmd,
            ),
            'share'   => array(
                'image' => !empty($this->_imageItems[0]) ? $this->_imageItems[0]['image'] : '',
                'title' => $this->_meta['title'] ? $this->_meta['title'] : '',
                'url'   => $this->_shareUrl,
            ),
        );
    }

    /**
     * 设置发布者信息
     * @param $vType
     * @return bool
     */
    private function _setUserData($vType) {

        $photo = $this->_getUserPhoto();
        if (!$photo) {
            return false;
        }
        $this->_tpl['data']['user'] = array(
            'user' => array(
                'photo' => $photo,
                'name'  => array(
                    'text'        => $this->_source,
                    'create_time' => strval($this->_meta['ts'] / 1000),
                ),
                'cmd'   => $this->_getUserCmd(),
                'vtype' => $vType,
                'v_url' => isset($this->_flagV[$vType]) ? $this->_flagV[$vType] : '',
            ),
        );
        return true;
    }

    /**
     * 获取发布者点击schema
     * @return string
     */
    private function _getUserCmd() {

        $userCmd = '';

        $style = array(
            'showtoolbar' => '1',
            'menumode' => '2',
            'toolbaricons' => array(
                'toolids' => array(
                    '3',
                ),
            ),
        );
        $slog = array(); //todo

        $uk = $this->_meta['displaytype_exinfo']['uk'] ? strval($this->_meta['displaytype_exinfo']['uk']) : '';
        if (isset($this->_meta['displaytype_exinfo']) && in_array($this->_sourceFrom, array('ugc', 'ugc_baijiahao'))) {
            $uk = Mbd_Account_Profile::getUkByUid($this->_meta['displaytype_exinfo']['uid']);
            $this->_meta['displaytype_exinfo']['uk'] = $uk;
        }
        if ($uk) {
            $context = array(
                'uk'   => $uk,
                'from' => 'feed',
                'ext'  => 'tab=dynamic',
            );
            $userCmd = sprintf(self::HOME_LAND_HYBRID, rawurlencode(json_encode($context)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
        } elseif (isset($this->_meta['displaytype_exinfo']['mr_id'])) {
            $context = array(
                'mr_id' => $this->_meta['displaytype_exinfo']['mr_id'],
                "from"  => "feed",
            );
            $userCmd = sprintf(self::HOME_LAND_HYBRID, rawurlencode(json_encode($context)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
        }

        return $userCmd;
    }

    /**
     * 获取发布者头像
     * @return bool|string
     */
    private function _getUserPhoto() {
        $photo = '';
        if ($this->_meta['displaytype_extinfo']['https_avatar']) {

            $photo = $this->_meta['displaytype_extinfo']['https_avatar'] . '&access=' . $this->_tnToken['access'];
        } elseif ($this->_meta['author_img']['original']['url']) {

            $photo = $this->_meta['author_img']['original']['url'] . '&access=' . $this->_tnToken['access'];
        }
        if ($photo) {
            return $photo;
        }
        return false;
    }

    /**
     * 设置大图 直接使用原图
     */
    private function _setBigImage() {
        if (!empty($this->_meta['gimageurls'][0]['url'])) {
            $this->_bigImage = Utils_Image::feedHttpToHttps($this->_meta['gimageurls'][0]['url'] . '&access=' . $this->_tnToken['access']);
        } elseif (!empty($this->_meta['imageurls'][0]['url'])) {
            $this->_bigImage = Utils_Image::feedHttpToHttps($this->_meta['imageurls'][0]['url'] . '&access=' . $this->_tnToken['access']);
        }
    }

    /**
     * 设置多图 直接使用原图
     */
    private function _setImageItems() {

        foreach ($this->_meta['imageurls'] as $imageItem) {

            $this->_imageItems[] = array(
                'image' => Utils_Image::feedHttpToHttps($imageItem['url'] . '&access=' . $this->_tnToken['access']),
            );
        }
    }

    /**
     * 构建图文类模板
     * @return bool
     */
    private function _buildNewsTpl() {

        $title = $this->_meta['title'] ? $this->_meta['title'] : '';

        $imageCount = count($this->_imageItems);
        if ($this->_bigImage && $imageCount == 1) {

            //单图
            $isImage = ($this->_meta['_set_mc_imgcnt'] && $this->_meta['_set_mc_imgcnt'] > 1);
            $this->_tpl = array(
                'layout' => 'star_bigimage',
                'data'   => array(
                    'title'    => $title,
                    'source'   => '',
                    'image'    => $this->_bigImage,
                    'type'     => $isImage ? 'image' : '',
                    'duration' => $isImage ? $this->_meta['_set_mc_imgcnt'] . '图' : '',
                ),
            );
        } elseif ($imageCount >= 2) {
            //多图（2-9）
            $this->_tpl = array(
                'layout' => 'star_image3',
                'data'   => array(
                    'title'      => $title,
                    'source'     => '',
                    'items'      => $this->_imageItems,
                ),
            );
        } else {
            //纯文字
            $this->_tpl = array(
                'layout' => 'star_text',
                'data'   => array(
                    'text0'  => $title,
                    'source' => '',
                ),
            );
        }

        //commentCmd
        $nid = $this->_item['nid'];
        $context = array(
            'feed_id' => $nid,
            'from'    => 'feed',
        );
        $commentContext = array(
            'feed_id' => $nid,
            'from'    => 'feed',
            'anchor'  => 'comment',
        );

        if (in_array($this->_sourceFrom, array('ugc', 'ugc_baijiahao'))) {
            $context['ugc'] = 1;
            $commentContext['ugc'] = 1;
            $url = sprintf(self::UGC_LAND_URL, rawurlencode(json_encode($context)));

            $slog = array(); //todo slog打点需要PM来定
            $style = array(
                'toolbaricons' => $this->_toolBarIcons,
                'menumode'     => $this->_menuMode,
            );
            $this->_tpl['data']['cmd'] = sprintf(self::UGC_LAND_HYBRID, rawurlencode(json_encode($context)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
            $this->_commentCmd = sprintf(self::UGC_LAND_HYBRID, rawurlencode(json_encode($commentContext)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
        } else {
            $url = sprintf(self::STAR_LAND_URL, rawurlencode(json_encode($context)));

            $slog = array(); //todo slog打点需要PM来定
            $style = array(
                'toolbaricons' => $this->_toolBarIcons,
                'menumode'     => $this->_menuMode,
            );
            $this->_tpl['data']['cmd'] = sprintf(self::UGC_LAND_HYBRID, rawurlencode(json_encode($context)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
            $this->_commentCmd = sprintf(self::STAR_LAND_HYBRID, rawurlencode(json_encode($commentContext)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
        }

        $this->_shareUrl = $url;
        return true;
    }

    /**
     * 构建视频模板
     * @return bool
     */
    private function _buildVideoTpl() {
        $nid = $this->_meta['displaytype_exinfo']['vid'] ? $this->_meta['displaytype_exinfo']['vid'] : '';
        if (empty($nid)) {
            return false;
        }
        $nid = strpos($nid, 'sv') === 0 ? $nid : 'sv_' . $nid;

        $title = $this->_meta['title'] ? $this->_meta['title'] : '';

        if (!$this->_bigImage) {
            return false;
        }
        $this->_likeExt['vid'] = $nid;

        $this->_tpl = array(
            'layout' => 'bigimage',
            'data'   => array(
                'title' =>  $title,
                'source' => '',
                'image'  => $this->_bigImage,
                'type'   => 'video',
                'duration' => isset($this->_meta['displaytype_exinfo']['long']) ? $this->_formatDuration($this->_meta['displaytype_exinfo']['long']) : '',
            ),
        );
        $slog = array(); //todo

        $url = self::FEED_VIDEO_URL . '?context='. urlencode(json_encode(array('nid' => $nid, 'sourceFrom' => 'starvideo')));
        $this->_commentCmd = $this->_tpl['data']['cmd'] = sprintf(self::FEED_VIDEO_LAND, rawurlencode($url), rawurlencode(json_encode($this->_toolBarIcons)), $this->_menuMode, rawurlencode(json_encode($slog)));

        $this->_shareUrl = $url;
        return true;
    }

    /**
     * 格式化视频时长
     * @param $seconds
     * @return string
     */
    private function _formatDuration($seconds){
        $durationSec = (int)$seconds;
        $hour = floor($durationSec / 3600);
        $hourSecond = $durationSec - $hour * 3600;
        $minute = floor($hourSecond / 60);
        $second = floor($hourSecond - $minute * 60);
        if ($hour) {
            $duration = sprintf('%02d:%02d:%02d', $hour, $minute, $second);
        } else {
            $duration = sprintf('%02d:%02d', $minute, $second);
        }
        return $duration;
    }
}
