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
     * @desc
     * @param 
     * @return 
     */
    protected function run() {
        
        // meta获取
        $uid = 621388556;
        $tplHandler = new Service_Data_Items();
        $retItems = $tplHandler->getItems($uid);

        $rnData = array();
        $naData = array();

        $rnTemplateHandler = new Service_Data_Rntemplate($this->requests);
        $naTemplateHandler = new Service_Data_Natemplate($this->requests);
        foreach ($retItems as $item) {
            ($rnItem = $rnTemplateHandler->buildTemplate($item)) != false && $rnData[] = $rnItem;
            ($naItem = $naTemplateHandler->buildTemplate($item)) != false && $naData[] = $naItem;
        }

        return array(
            'rn' => $rnData,
            'na' => $naData,
        );
                
    }
}
