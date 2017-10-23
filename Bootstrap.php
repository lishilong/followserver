<?php

/**
 * Bootstrap
 * @desc 项目初始化
 * @ctime 2015-06-04
 */
class Bootstrap extends Ap_Bootstrap_Abstract {

    public function _initRoute(Ap_Dispatcher $dispatcher) {
        $router = Ap_Dispatcher::getInstance()->getRouter();
        $route = new Resbox_Route();
        $router->addRoute('Followserver', $route);
    }

    public function _initView(Ap_Dispatcher $dispatcher) {
        $dispatcher->disableView();
    }

    public function _initPlugin(Ap_Dispatcher $dispatcher) {
        $statLogPlugin = new Plugin_StatLog();
        $dispatcher->registerPlugin($statLogPlugin);
    }

    public function _initNocache(Ap_Dispatcher $dispatcher) {
        //禁止浏览器缓存
        if(php_sapi_name()!='cli'){
            header("Expires: Mon, 26 Jul 2000 00:00:00 GMT");
            header("Cache-Control: no-store, must-revalidate");
            header("Pragma: no-cache");
        }
    }

}
