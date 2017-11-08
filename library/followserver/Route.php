<?php

/**
 * Followserver_Route
 * @desc 自动定义路由
 * @author baijianmin
 * @ctime 2015-6-4
 */
class Followserver_Route implements Ap_Route_Interface {

    /**
     * 设置路由
     * @param obj $request
     * @return boolean
     */
    public function route($request) {
        $requestGet = $request->getQuery();

        $controller = isset($requestGet['controller']) ? $requestGet['controller'] : 'Error';
        $action = isset($requestGet['action']) ? $requestGet['action'] : 'Index';

        $request->setControllerName(ucfirst($controller));
        $request->setActionName($action);

        $api = $controller . "_" . $action;
        Bd_Log::addNotice('api', $api);

        return true;
    }
}
