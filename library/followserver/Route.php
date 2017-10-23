<?php

/**
 * Followserver_Route
 * @desc 自动定义路由
 * @author baijianmin
 * @ctime 2015-6-4
 */
class Followserver_Route implements Ap_Route_Interface {
    private static $pathInfo=array();
    /**
     * 设置路由
     * @param obj $request
     * @return boolean
     */
    public function route($request) {
        Utils_Timer::init();
        $_requestGet = $request->getQuery();
        //路由白名单
        $routeConfig = Bd_Conf::getAppConf('route');
        if (empty($routeConfig) || !is_array($routeConfig) || count($routeConfig) < 1) {
            //Bd_Log::fatal('Route config err');
        }
        
        if(!empty($_SERVER["PATH_INFO"]) && strlen($_SERVER["PATH_INFO"])>3 && $this->_routeRestful($request,trim($_SERVER["PATH_INFO"],"/")) ){
            return true;
        }

        $_service = 'api';
        $_action = 'default';
        if (isset($_requestGet['ctl']) && isset($_requestGet['action'])) {
            $c = $_requestGet['ctl'];
            $a = $_requestGet['action'];
            if (isset($routeConfig[$c]) && isset($routeConfig[$c][$a]) && $routeConfig[$c][$a] == 1) {
                $_service = $c;
                $_action = $a;
            }
        }
//         Bd_Log::addNotice('ctl', $_service);
//         Bd_Log::addNotice('action', $_action);
        $_type='';
        if (isset($_requestGet['type'])) {
//             Bd_Log::addNotice('type', $_requestGet['type']);
            $_type=$_requestGet['type'];
        }
        
        self::$pathInfo=array(
            'ctl'=>$_service,
            'action'=>$_action,
            'type'=>$_type,
        );
        $request->setControllerName(ucfirst($_service));
        $request->setActionName($_action);
        return true;
    }
    
    /**
     * 路由解析-restful 支持
     * @param Ap_Request_Http $request
     * @param string $path_info
     * @return boolean
     */
    private function _routeRestful($request,$path_info){
        $arr=explode('/', $path_info);
        if($arr[0]=="relation"){
            array_unshift($arr, "subresource");
        }
        
        if(count($arr)>=3){
            $_ctl=$arr[0];
            $_action=$arr[1];
            $_type=$arr[2];
            
            //将 semimedia_trans 处理成为  semimediaTrans 这样
            // 保证ps是统一的3层的结构
            $_tmp = explode('_', $_type);
            $_tmpArr = array_map('ucfirst', $_tmp);
            $_tmpArr[0] = $_tmp[0];
            $_type = implode('', $_tmpArr);
            
            self::$pathInfo=array(
                'ctl'=>$_ctl,
                'action'=>$_action,
                'type'=>$_type,
            );
//             Bd_Log::addNotice('ctl', $_ctl);
//             Bd_Log::addNotice('action', $_action);
//             Bd_Log::addNotice('type', $_type);
            
            $request->setControllerName($_ctl);
            $request->setActionName($_action);
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param string $key
     * @return string
     */
    public static function getPathInfo($key){
        return isset(self::$pathInfo[$key])?self::$pathInfo[$key]:"";
    }
    
    /**
     * 获取当前的api名称
     * @return string
     */
    public static function getApi(){
        $ctl=self::getPathInfo('ctl');
        $action=self::getPathInfo('action');
        $type=self::getPathInfo('type');
        $api = lcfirst($ctl).'_'.lcfirst($action).'_'.lcfirst($type);
        return $api;
    }
}
