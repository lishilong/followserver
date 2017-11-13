<?php
/**
 * Created by PhpStorm.
 * User: chenhanchuan
 * Date: 17/11/11
 * Time: 下午2:46
 */

class Service_Data_Rntemplate extends Service_Data_Base {

    const UGC_LAND_URL = 'https://mbd.baidu.com/webpage?type=user&action=dynamic&context=%s';
    const STAT_LAND_URL = 'https://mbd.baidu.com/webpage?type=celebrity&action=dynamic&context=%s';
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

    private $_tpl = null;
    private $_itemType = 1; // 1: 纯文字 2: 一图（横图） 3: 一图（纵图）4: 多图 9: 视频
    private $_sourceFrom = null;
    private $_bigImage = '';
    private $_imageItems = array();

    private $_toolBarIcons = array(
        'toolids'=>array(// 1、2、3 和4不同时出现，会双bar
            '1',//评论（显示评论数）
            '2',//收藏
            '3',//分享
            //'4',//评论输入框
        ),
    );
    private $_menuMode = '2';//可选 1:纯H5页面菜单。收藏，下载，刷新，分享，设置，意见反馈 2:feed 菜单。 刷新，复制，意见反馈，iOS 多一个夜间模式 3:NA 菜单。收藏，下载，设置，意见反馈

    // V标图片
    private $_flagV = array(
        0 => '', //none
        1 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460792824.png', //golden
        2 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460970589.png', //blue
        3 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460903468.png', //yellow
    );

    /**
     * Rntemplate constructor.
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
        $this->_tpl = null;
        $this->_itemType = 1; // 1: 纯文字 2: 一图（横图） 3: 一图（纵图）4: 多图 9: 视频
        $this->_sourceFrom = null;
        $this->_bigImage = '';
        $this->_imageItems = array();
    }

    /**
     * 构建通用模板
     * @return bool
     */
    private function _buildNormalTemplate() {

        if (!$this->_setItemType()) {
            return false;
        }
        $this->_tpl['scroll_id'] = isset($this->_item['scroll_id']) ? $this->_item['scroll_id'] : '';

        if (!($photo = $this->_getUserPhoto())) {
            return false;
        }

        $this->_sourceFrom = $this->_meta['displaytype_exinfo']['source'] ? $this->_meta['displaytype_exinfo']['source'] : '';
        if ($this->_sourceFrom == 'ugc' || $this->_sourceFrom == 'ugc_baijiahao') {
            $source = $this->_meta['displaytype_exinfo']['display_name'] ? $this->_meta['displaytype_exinfo']['display_name'] : '';
        } else {
            $source = $this->_meta['site'] ? $this->_meta['site'] : '';
        }

        $vType = isset($this->_meta['displaytype_exinfo']['is_authenticated']) ? strval($this->_meta['displaytype_exinfo']['is_authenticated']) : '';

        $title = $this->_meta['title'] ? $this->_meta['title'] : '';

        //分享url
        if ($this->_itemType == 9) {
            //视频
            $vid = $this->_meta['displaytype_exinfo']['vid'] ? $this->_meta['displaytype_exinfo']['vid'] : '';
            if (empty($vid)) {
                return false;
            }
            $vid = strpos($vid, 'sv') === 0 ? $vid : 'sv_' . $vid;
            $shareUrl = self::FEED_VIDEO_URL . '?context='. urlencode(json_encode(array('nid' => $vid, 'sourceFrom' => 'starvideo')));

            $slog = array(); //todo
            $cmd = $commentCmd = sprintf(self::FEED_VIDEO_LAND, rawurlencode($shareUrl), rawurlencode(json_encode($this->_toolBarIcons)), $this->_menuMode, rawurlencode(json_encode($slog)));
        } else {
            //图文
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
                $slog['page'] = 'ugc_detailpage';
                $context['ugc'] = 1;
                $commentContext['ugc'] = 1;
                $shareUrl = sprintf(self::UGC_LAND_URL, rawurlencode(json_encode($context)));

                $slog = array(); //todo
                $style = array(
                    'toolbaricons' => $this->_toolBarIcons,
                    'menumode'     => $this->_menuMode,
                );
                $cmd = sprintf(self::UGC_LAND_HYBRID, rawurlencode(json_encode($context)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
                $commentCmd = sprintf(self::UGC_LAND_HYBRID, rawurlencode(json_encode($commentContext)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
            } else {
                $slog['page'] = 'star_detailpage';
                $shareUrl = sprintf(self::STAT_LAND_URL, rawurlencode(json_encode($context)));

                $slog = array(); //todo
                $style = array(
                    'toolbaricons' => $this->_toolBarIcons,
                    'menumode'     => $this->_menuMode,
                );
                $cmd = sprintf(self::UGC_LAND_HYBRID, rawurlencode(json_encode($context)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
                $commentCmd = sprintf(self::UGC_LAND_HYBRID, rawurlencode(json_encode($commentContext)), rawurlencode(json_encode($style)), rawurlencode(json_encode($slog)));
            }
        }

        $this->_tpl['itemData'] = array(
            'rid'  => $this->_sourceFrom . '_' . $this->_item['nid'],
            'user' => array(
                'photo' => $photo,
                'name_text' => $source,
                'update_time' => strval($this->_meta['ts'] / 1000),
                'cmd' => array(
                    'mode' => $this->_menuMode,
                    'url'  => $this->_getUserCmd(),
                ),
                'vtype' => $vType,
                'v_url' => isset($this->_flagV[$vType]) ? $this->_flagV[$vType] : '',
            ),
            'title' => $title,
            'cmd' => array(
                'mode' => $this->_menuMode,
                'url' => $cmd,
            ),
            'read_num' => isset($this->_item['read_count']) ? $this->_item['read_count'] : '',
            'domainname' => '',
            'zan' => array(
                'praise'  => isset($this->_like['count']) ? $this->_like['count'] : '',
                'degrade' => '0', //踩的数量，固定置0
                'userOp'  => $this->_like['type'] == '1' ? true : false,
                't'       => '', //端上没用到
                'nid'     => $this->_meta['nid'],
            ),
            'comment_num' => isset($this->_comment['count']) ? $this->_comment['count'] : '',
            'cmdtocomment' => array(
                'mode' => $this->_menuMode,
                'url'  => $commentCmd,
            ),
            'shareInfo' => array(
                'url' => $shareUrl,
                'title' => $title,
            ),
            'poster' => array(
                'is_gif' => 0,
                'url'    => '',
                'width'  => '',
                'height' => '',
            ),
        );

        if (in_array($this->_itemType, array(2, 3))) {
            $this->_setBigImage();
            $this->_setImageItems();
            $this->_tpl['itemData']['thumbnail'][] = $this->_bigImage;
        } elseif ($this->_itemType == 4) {
            $this->_setImageItems();
            foreach ($this->_imageItems as $imageItem) {
                $this->_tpl['itemData']['thumbnail'][] = $imageItem['image'];
            }
        } elseif ($this->_itemType == 9) {
            $this->_setBigImage();
            $this->_setImageItems();
            $this->_tpl['itemData']['thumbnail'][] = $this->_bigImage;

            $this->_tpl['itemData']['duration'] = isset($this->_meta['displaytype_exinfo']['long']) ? $this->_formatDuration($this->_meta['displaytype_exinfo']['long']) : '';
        }

        if (!empty($this->_imageItems[0])) {
            $this->_tpl['itemData']['poster'] = array(
                'is_gif' => 0,
                'url'    => $this->_imageItems[0]['image'],
                'width'  => $this->_meta['imageurls'][0]['width'] ? $this->_meta['imageurls'][0]['width'] : 0,
                'height' => $this->_meta['imageurls'][0]['height'] ? $this->_meta['imageurls'][0]['height'] : 0,
            );
        }

        return true;
    }

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
     * 设置模板类型
     * @return bool
     */
    private function _setItemType() {
        if ($this->_meta['type'] == 'text') {

            $imageUrls = $this->_meta['imageurls'] ? $this->_meta['imageurls'] : array();
            $imageCount = count($imageUrls);
            if ($imageCount == 0) {
                //纯文字
                $this->_itemType = 1;
                $this->_tpl['itemType'] = 'follow_titleonly';
            } elseif ($imageCount == 1) {
                //单图
                if ($this->_meta['imageurls']['height'] < $this->_meta['imageurls']['width']) {
                    $this->_itemType = 2;
                    $this->_tpl['itemType'] = 'follow_horizontal_image';
                } else {
                    $this->_itemType = 3;
                    $this->_tpl['itemType'] = 'follow_vertical_image';
                }
            } else {
                //多图
                $this->_itemType = 4;
                $this->_tpl['itemType'] = 'follow_multi_image';
            }

        } elseif ($this->_meta['type'] == 'videolive') {
            //视频
            $this->_itemType = 9;
            $this->_tpl['itemType'] = 'follow_video';
        } else {
            return false;
        }
        return true;
    }

    /**
     * 构建横滑模板
     * @return bool
     */
    private function _buildRecommendTemplate() {
        return true;
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