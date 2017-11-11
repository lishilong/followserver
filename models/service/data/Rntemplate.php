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
    const FEED_VIDEO_LAND = 'https://sv.baidu.com/videoui/page/videoland';

    private $_requests = null;
    private $_bdVerison = null;
    private $_tnToken = null;
    private $_supportWebp = false;

    private $_item = null;
    private $_meta = null;
    private $_like = null;
    private $_comment = null;

    private $_tpl = null;
    private $_itemType = 1; // 1: 纯文字 2: 一图（横图） 3: 一图（纵图）4: 多图 9: 视频
    private $_sourceFrom = null;
    private $_source = null;

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
        $this->_requests = $requests;
        $this->_supportWebp = $this->_requests['webp'] == 'webp'; //端上是否支持展现webp图像
        $this->_bdVersion = isset($this->_requests['bd_version']) ? $this->_requests['bd_version'] : '0';
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
     * 构建通用模板
     * @return bool
     */
    private function _buildNormalTemplate() {

        if (!$this->_setItemType()) {
            return false;
        }
        $this->_tpl['scroll_id'] = $this->_item['scroll_id'];

        if (!($photo = $this->_getUserPhoto())) {
            return false;
        }

        $this->_sourceFrom = $this->_meta['displaytype_exinfo']['source'] ? $this->_meta['displaytype_exinfo']['source'] : '';
        if ($this->_sourceFrom == 'ugc' || $this->_sourceFrom == 'ugc_baijiahao') {
            $this->_source = $this->_meta['displaytype_exinfo']['display_name'] ? $this->_meta['displaytype_exinfo']['display_name'] : '';
        } else {
            $this->_source = $this->_meta['site'] ? $this->_meta['site'] : '';
        }

        $vType = $this->_meta['displaytype_exinfo']['is_authenticated'] ? strval($this->_meta['displaytype_exinfo']['is_authenticated']) : '';

        $title = $this->_meta['title'] ? $this->_meta['title'] : '';

        //分享url
        if ($this->_itemType == 9) {
            //视频
            $nid = $this->_meta['displaytype_exinfo']['vid'] ? $this->_meta['displaytype_exinfo']['vid'] : '';
            if (empty($nid)) {
                return false;
            }
            $nid = strpos($nid, 'sv') === 0 ? $nid : 'sv_' . $nid;
            $shareUrl = self::FEED_VIDEO_LAND . '?context='. urlencode(json_encode(array('nid' => $nid, 'sourceFrom' => 'starvideo')));
        } else {
            //图文
            $nid = $this->_item['nid'];
            $context = array(
                'feed_id' => $nid,
                'from'    => 'feed',
            );
            if (in_array($this->_sourceFrom, array('ugc', 'ugc_baijiahao'))) {
                $slog['page'] = 'ugc_detailpage';
                $context['ugc'] = 1;
                $commentContext['ugc'] = 1;
                $shareUrl = sprintf(self::UGC_LAND_URL, rawurldecode(json_encode($context)));
            } else {
                $slog['page'] = 'star_detailpage';
                $shareUrl = sprintf(self::STAT_LAND_URL, rawurldecode(json_encode($context)));
            }
        }

        $this->_tpl['itemData'] = array(
            'rid'  => 'type_' . $this->_item['nid'],
            'user' => array(
                'photo' => $photo,
                'name_text' => $this->_source,
                'update_time' => strval($this->_meta['ts'] / 1000),
                'cmd' => array(
                    'mode' => $this->_menuMode,
                    'url'  => '', //todo
                ),
                'vtype' => $vType,
                'v_url' => isset($this->_flagV[$vType]) ? $this->_flagV[$vType] : '',
            ),
            'title' => $title,
            'cmd' => array(
                'mode' => $this->_menuMode,
                'url' => '', //todo
            ),
            'read_num' => $this->_item['read_count'],
            'domainname' => '', //todo
            'zan' => array(
                'praise'  => $this->_like['count'] ? $this->_like['count'] : '',
                'degrade' => '0', //todo
                'userOp'  => $this->_like['type'] == '1' ? true : false,
                't'       => '', //todo
                'nid'     => $this->_meta['nid'],
            ),
            'comment_num' => $this->_comment['count'] ? $this->_comment['count'] : '',
            'cmdtocomment' => array(
                'mode' => $this->_menuMode,
                'url'  => ''//todo
            ),
            'shareInfo' => array(
                'url' => $shareUrl,
                'title' => $title,
            ),
            'poster' => array(   //todo
                'is_gif' => 0,
                'url'    => '',
                'width'  => '',
                'height' => '',
            ),
        );

        if (in_array($this->_itemType, array(2, 3, 4))) {
            foreach ($this->_meta['imageurls'] as $imageurl) {
                $this->_tpl['itemData']['thumbnail'][] = $imageurl['url'];
            }
        } elseif ($this->_itemType == 9) {
            if ($this->_meta['gimageurls'][0]['url']) {
                $this->_tpl['itemData']['thumbnail'][] = $this->_meta['gimageurls'][0]['url'];
            } elseif ($this->_meta['imageurls'][0]['url']) {
                $this->_tpl['itemData']['thumbnail'][] = $this->_meta['imageurls'][0]['url'];
            }
            $this->_tpl['itemData']['duration'] = isset($this->_meta['displaytype_exinfo']['long']) ? $this->_formatDuration($this->_meta['displaytype_exinfo']['long']) : '';
        }

        return true;
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
        $this->_source = null;
    }

    /**
     * 获取发布者头像
     * @return bool|string
     */
    private function _getUserPhoto() {
        $photo = '';
        if ($this->_meta['displaytype_extinfo']['https_avatar']) {
            $photo = $this->_meta['displaytype_extinfo']['https_avatar'];
            if ($this->_supportWebp) {
                //todo
            }
        } elseif ($this->_meta['author_img']['original']['url']) {
            $photo = $this->_meta['author_img']['original']['url'];
            if ($this->_supportWebp) {
                //todo
            }
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