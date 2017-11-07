<?php
/**
 * @filename: models/service/data/Template.php
 * @desc 拼装模板数据 
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-07 15:26:47
 * @last modify 2017-11-07 15:26:47
 */

class Service_Data_Template extends Service_Data_Base
{

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
     * @desc 拼装RN模板1图模板（横图或者竖图）
     * @param string nid
     * @param string layout 默认为follow_horizontal_image RN横图1图模板 
     * @return 
     */
    public function buildTpl($meta){ 
        $tempalteData = array();
        $user = self::getUserInfoFromFeedMeta($meta);     
    }

    public function getUserInfoFromFeedMeta(&$meta) {
        $user = array();
        if (isset($meta['displaytype_exinfo']['http_avatar'])) {
            $user['photo'] = $meta['displaytype_exinfo']['http_avatar'];
        }
        $user['name']  = $meta[];
        $user['desc']  = $meta[];
        $user['cmd']   = $meta[];
        $user['vtype'] = $meta[];
    }
  
}
