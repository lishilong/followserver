<?php
/*
 * Service_Data_Nidpolicy
 * 去关注频道策略server取nid列表
 *
 * @author weiyanjiang [weiyanjiang@baidu.com]
 * @version 1.0
 */

class Service_Data_Nidpolicy extends Service_Data_Base{

    public function execute(){}

    /**
     * @brief 去策略server获取nid列表
     * @param array $nids
     * @return array
     */
    public function getNids(){
        $nids = array(122550092327743297);
        return $nids;
    }

}
