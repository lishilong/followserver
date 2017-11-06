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
    public function getNids(){
        $nids = array(
                "678741571145693057",
                "17991983688588921690"
                );
        return $nids;
    }

}
