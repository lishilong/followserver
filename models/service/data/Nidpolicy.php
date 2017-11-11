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
    public function getNids($requests){
       //获取数据
        $data = array(
            'loc_city' => isset($requests['loc_city']) ? $requests['loc_city'] : '',
            'ip'        => isset($requests['id']) ? $requests['id'] : '',
            'cuid'      => isset($requests['cuid']) ? $requests['cuid'] : '',
            'network'   => isset($requests['network']) ? $requests['network'] : '',
            'os_version'=> isset($requests['os_version']) ? $requests['os_version'] : '',
            'sid'        => isset($requests['sid']) ? $requests['sid'] : '',
            'app_version'=> isset($requests['app_version']) ? $requests['app_version'] : '',
            'loc_district' => isset($requests['loc_district']) ? $requests['loc_district'] : '',
            'osname'       => isset($requests['osname']) ? $requests['osname'] : '',
            'osbranch'     => isset($requests['osbranch']) ? $requests['osbranch'] : '',
            'pkgname'      => isset($requests['pkgname']) ? $requests['pkgname'] : '',
            'loc_province'=> isset($requests['loc_province']) ? $requests['loc_province'] : '',
            'command'     => isset($requests['command']) ? $requests['command'] : '',
            'ut'            => isset($requests['ut']) ? $requests['ut'] : '',
            'ua'           => isset($requests['ua']) ? $requests['ua'] : '',
            'front_type'=> isset($requests['front_type']) ? intval($requests['front_type']) : 0,
            'city_code'  => isset($requests['city_code']) ? intval($requests['city_code']) : 0,
            'refresh_type' => isset($requests['refresh_type']) ? intval($requests['refresh_type']) : 0,
            'product'     => isset($requests['product']) ? intval($requests['product']) : 0,
            'refresh_state'=> isset($requests['refresh_state']) ? intval($requests['refresh_state']) : 0,
            'refresh_count' => isset($requests['refresh_count']) ? intval($requests['refresh_count']) : 0,
            'channel_id'  => isset($requests['channel_id']) ? intval($requests['channel_id']) : 0,
            'blacklist_timestamp' => isset($requests['blacklist_timestamp']) ? intval($requests['blacklist_timestamp']) : 0,
            'subscribe' => isset($requests['subscribe']) ? $requests['subscribe'] : array(),
            'smfw'          => isset($requests['smfw']) ? $requests['smfw'] : array(),
            'filter'      => isset($requests['filter']) ? $requests['filter'] : array(),
            'baijiahao_sub'=> isset($requests['baijiahao_sub']) ? $requests['baijiahao_sub'] : array(),
            'loc_point'    => isset($requests['loc_point']) ? $requests['loc_point'] : array(),
            'context'     => isset($requests['context']) ? $requests['context'] : array()
        );

        $nids = Utils_Ral_RestapiGolang::getInstance()->getStreamData($data);

        return $nids;
    }

}
