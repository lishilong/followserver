<?php
/**
 * @filename: actions/follow/Follow.php
 * @desc
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:12:16
 * @last modify 2017-11-01 17:12:16
 */

class Action_Follow extends Followserver_BaseAction {

    /**
     * @param $requests
     * @return bool
     */
    protected function run($requests) {

        $objServicePagePa = new Service_Page_Follow_Follow();
        return $objServicePagePa->execute($requests);
    }
}
