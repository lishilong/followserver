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
        $arrResult = array(
                'errno' => '-1',
                'timestamp'=>'0',
                'data' => array()
                );

        $time = time();

        // meta获取
        $nids = Service_Data_Nidpolicy::getNids();
        $tplHandler = new Service_Data_Items();
        $uid = 621388556;
        $ret = $tplHandler->getItemsFromNids($nids, true, $uid);
                
       // echo json_encode($arrResult);
    }
}
