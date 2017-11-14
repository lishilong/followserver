<?php
/**
 * @filename: models/service/data/Nidpolicy.php
 * @desc 去关注频道策略server取nid列表
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:19:23
 * @last modify 2017-11-01 17:19:23
 */

class Service_Data_Nidpolicy extends Service_Data_Base{

    /**
     * @brief 去策略server获取nid列表
     * @param array $nids
     * @return array
     */

 /*   public function getNids($requests)
    {
        //获取数据
        $data = array(
            'loc_city' => isset($requests['loc_city']) ? $requests['loc_city'] : '',
            'ip' => isset($requests['id']) ? $requests['id'] : '',
            'cuid' => isset($requests['cuid']) ? $requests['cuid'] : '',
            'network' => isset($requests['network']) ? $requests['network'] : '',
            'os_version' => isset($requests['os_version']) ? $requests['os_version'] : '',
            'sid' => isset($requests['sid']) ? $requests['sid'] : '',
            'app_version' => isset($requests['app_version']) ? $requests['app_version'] : '',
            'loc_district' => isset($requests['loc_district']) ? $requests['loc_district'] : '',
            'osname' => isset($requests['osname']) ? $requests['osname'] : '',
            'osbranch' => isset($requests['osbranch']) ? $requests['osbranch'] : '',
            'pkgname' => isset($requests['pkgname']) ? $requests['pkgname'] : '',
            'loc_province' => isset($requests['loc_province']) ? $requests['loc_province'] : '',
            'command' => isset($requests['command']) ? $requests['command'] : '',
            'ut' => isset($requests['ut']) ? $requests['ut'] : '',
            'ua' => isset($requests['ua']) ? $requests['ua'] : '',
            'front_type' => isset($requests['front_type']) ? intval($requests['front_type']) : 0,
            'city_code' => isset($requests['city_code']) ? intval($requests['city_code']) : 0,
            'refresh_type' => isset($requests['refresh_type']) ? intval($requests['refresh_type']) : 0,
            'product' => isset($requests['product']) ? intval($requests['product']) : 0,
            'refresh_state' => isset($requests['refresh_state']) ? intval($requests['refresh_state']) : 0,
            'refresh_count' => isset($requests['refresh_count']) ? intval($requests['refresh_count']) : 0,
            'channel_id' => isset($requests['channel_id']) ? intval($requests['channel_id']) : 0,
            'blacklist_timestamp' => isset($requests['blacklist_timestamp']) ? intval($requests['blacklist_timestamp']) : 0,
            'subscribe' => isset($requests['subscribe']) ? $requests['subscribe'] : array(),
            'smfw' => isset($requests['smfw']) ? $requests['smfw'] : array(),
            'filter' => isset($requests['filter']) ? $requests['filter'] : array(),
            'baijiahao_sub' => isset($requests['baijiahao_sub']) ? $requests['baijiahao_sub'] : array(),
            'loc_point' => isset($requests['loc_point']) ? $requests['loc_point'] : array(),
            'context' => isset($requests['context']) ? $requests['context'] : array()
        );

        $nids = Utils_Ral_RestapiGolang::getInstance()->getStreamData($data);
        return $nids;
    }
*/

    /**
     * @desc
     * @param 
     * @return 
     */
    public static function getFeedList(){
        $mock='[{"id":"194981331293336627","display_strategy":{"category":0,"tag":2,"type":0,"mark":0,"templates":{"id":[-1]},"content":[]},"ext":{"ac":9,"tag":2,"mark":0,"rec_src":[4],"cs":"2229979592 2583121467","ua":"1080_1920_android_10.0.0.0_480","ut":"VTR-AL00_7.0_24_HUAWEI","province":"閸栨ぞ鍚敮锟�","city":"閸栨ぞ鍚敮锟�","district":"濞撮攱绌╅崠锟�","channel_id":1,"session_id":"1510123560718","refresh_index":"9","position":0,"log_id":2917608246,"scroll_id":"","refresh_timestamp_ms":1509692121744,"templates":{"id":[-1]}},"cs":"2229979592 2583121467","timestamp":1509692122258},{"id":"1xxxxxxxxxxxxxxxx","display_strategy":{"category":0,"tag":2,"type":0,"mark":0,"templates":{"id":[29]},"content":[{"account_name":"黄飞鸿","account_id":54,"account_md5":"","account_desc":"佛山武术大师","account_img":"http://www.baidu.com/resbox/a.jpg","account_vtype":"1","score":0.116636,"ts":1504623635,"recall_type":0,"vertical_type":0,"sample_name":""}]},"ext":{"ac":9,"tag":2,"mark":0,"rec_src":[4],"cs":"2229979592 2583121467","ua":"1080_1920_android_10.0.0.0_480","ut":"VTR-AL00_7.0_24_HUAWEI","province":"閸栨ぞ鍚敮锟�","city":"閸栨ぞ鍚敮锟�","district":"濞撮攱绌╅崠锟�","channel_id":1,"session_id":"1510123560718","refresh_index":"9","position":0,"log_id":2917608246,"scroll_id":"","refresh_timestamp_ms":1509692121744,"templates":{"id":[29]}},"cs":"2229979592 2583121467","timestamp":1509692122258},{"id":"11807010522114859551","display_strategy":{"category":0,"tag":2,"type":0,"mark":0,"templates":{"id":[-1]},"content":[]},"ext":{"ac":9,"tag":2,"mark":0,"rec_src":[4],"cs":"2229979592 2583121467","ua":"1080_1920_android_10.0.0.0_480","ut":"VTR-AL00_7.0_24_HUAWEI","province":"閸栨ぞ鍚敮锟�","city":"閸栨ぞ鍚敮锟�","district":"濞撮攱绌╅崠锟�","channel_id":1,"session_id":"1510123560718","refresh_index":"9","position":0,"log_id":2917608246,"scroll_id":"","refresh_timestamp_ms":1509692121744,"templates":{"id":[-1]}},"cs":"2229979592 2583121467","timestamp":1509692122258}]';
        $feedList = json_decode($mock, true);
        return $feedList;
    }

}
