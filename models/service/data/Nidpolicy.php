<?php
/**
 * @filename: models/service/data/Nidpolicy.php
 * @desc 去关注频道策略server取nid列表
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:19:23
 * @last modify 2017-11-01 17:19:23
 */

class Service_Data_Nidpolicy extends Service_Data_Base{

    public function execute(){}

    /**
     * @brief 去策略server获取nid列表
     * @param array $nids
     * @return array
     */
    public static function getNids(){
        $nids = array(
            "731105962789415638",//only wen
            "194981331293336627",//多图文
            "11807010522114859551"//视频
        );
        return $nids;
    }

}
