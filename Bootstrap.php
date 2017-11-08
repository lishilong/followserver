<?php
/**
 * @name Bootstrap
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Ap_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 * @author weiyanjiang@baidu.com
 */
class Bootstrap extends Ap_Bootstrap_Abstract{

    /**
     * @param Ap_Dispatcher $dispatcher
     */
    public function _initLoader(Ap_Dispatcher $dispatcher) {
        // 加载library
        $library = Ap_Loader::getInstance()->getLibraryPath();
        Ap_Loader::import($library . "/distribute/Error.php");
    }

    /**
     * @param Ap_Dispatcher $dispatcher
     */
    public function _initRoute(Ap_Dispatcher $dispatcher) {
        //在这里注册自己的路由协议,默认使用static路由
        $router = $dispatcher->getRouter();
        $route = new Followserver_Route();
        $router->addRoute('Followserver', $route);
    }


    /**
     * @param Ap_Dispatcher $dispatcher
     */
    public function _initPlugin(Ap_Dispatcher $dispatcher) {
        //注册saf插件
        $objPlugin = new Saf_ApUserPlugin();
        $statLogPlugin = new Plugin_StatLog();
        $dispatcher->registerPlugin($statLogPlugin);
        $dispatcher->registerPlugin($objPlugin);
    }


    /**
     * @param Ap_Dispatcher $dispatcher
     */
    public function _initView(Ap_Dispatcher $dispatcher){
        //在这里注册自己的view控制器，例如smarty,firekylin
        $dispatcher->disableView();//禁止ap自动渲染模板
    }
}
