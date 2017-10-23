<?php

/**
 * action 基类
 * @author baijianmin
 * @ctime 2015-6-4
 */
abstract class Resbox_BaseAction extends Ap_Action_Abstract {

    protected $requestParams;
    
    private $_enableJsonp=false;
    
    /**
     * 是否启用签名校验
     * @var string
     */
    protected $checkHttpSign = false;
    
    public function preExecute(){
        
    }
    
    public function preCheck(){
        
    }
    
    private $_requestStartTime=0;
    
    public function execute() {
        try {
            $this->_requestStartTime = microtime(true);
            
            $requestSrv = $this->getRequest();
            $this->requestParams = $requestSrv->getRequest();
            $this->preCheck();
            $this->checkParams($this->requestParams);

            $this->preExecute();
            
            $ctl=Resbox_Route::getPathInfo('ctl');
            $action=Resbox_Route::getPathInfo('action');
            $type=Resbox_Route::getPathInfo('type');
            
            $pageSrvName = 'Service_Page_' . ucfirst($ctl) . '_' .ucfirst($action) . '_' . ucfirst($type);

            $conf = Bd_Conf::getAppConf('pblog');

            $cateId = (int) $conf[$ctl.'_'.$action]['cateid'];
            $actionId = (int) $conf[$ctl.'_'.$action]['actionid'][$type];

            Box_Log_Pb::setCateid($cateId);
            Box_Log_Pb::setActionid($actionId);
            
            if(!class_exists($pageSrvName)){
                return $this->error(Resbox_Errno::ERRNO_COMMON_ROUTE_ERR,"pageService [{$pageSrvName}] not exists");
            }
            
            $pageSrv = new $pageSrvName();
            
            if(method_exists($pageSrv, "isEnableJsonp")){
                $this->_enableJsonp=$pageSrv->isEnableJsonp();
            }
            
            if(method_exists($pageSrv, "preExecute")){
                $pageSrv->preExecute();
            }
            
            $ret = $pageSrv->execute($this->requestParams['params']);
            
            if(method_exists($pageSrv, 'teardown')){
                $pageSrv->teardown();
            }
            
            if (false !== $ret) {
                //成功返回结果
                return $this->success($ret);
            } else {
                $errno = $pageSrv->getErrno();
                $errmsg = $pageSrv->getErrmsg();
                throw new Exception($errmsg, $errno);
                
            }
        } catch (Exception $e) {
            if($pageSrv && method_exists($pageSrv, 'teardown')){
                $pageSrv->teardown();
            }
            $errno = $e->getCode();
            empty($errno) && $errno=500;
            $errmsg = $e->getMessage();
            
            if (substr($errno.'', -3,1) != '2') {
                Mbd_Log_Txt::setErr();
            }
            
            Mbd_Log_Txt::setWarning($errno,$errmsg);
        }
        return $this->error($errno, $errmsg);
    }

    /**
     * 基本参数校验
     * @param type $this->requestParams
     * @return boolean
     * @throws Exception
     */
    protected function checkParams() {
        
        if (isset($this->requestParams['params'])) {
            Bd_Log::addNotice('params', $this->requestParams['params']);
            $this->requestParams['params'] = json_decode($this->requestParams['params'], true);
        }
        Bd_Log::addNotice('post',json_encode($_POST));
        Bd_Log::addNotice('get',json_encode($_GET));
        $ctl=Resbox_Route::getPathInfo('ctl');
        $action=Resbox_Route::getPathInfo('action');
        $type=Resbox_Route::getPathInfo('type');
        
        Mbd_Log_Txt::setApi(Resbox_Route::getApi());
        
        if($this->checkHttpSign){
            $this->checkHttpSignParams($ctl, $action, $type);
        }
        
        $checkList = Bd_Conf::getAppConf('params/' . $ctl . '/' . $action . '/' . $type);
        if (!$checkList) {
            return true;
        }
        foreach (array_keys($checkList) as $item) {
            if (isset($this->requestParams['params'][$item])) {
                if (is_string($this->requestParams['params'][$item]) && strlen($this->requestParams['params'][$item]) == 0) {
                    throw new Exception("argument[$item] is missing", 2000);
                } elseif (is_array($this->requestParams['params'][$item]) && empty($this->requestParams['params'][$item])) {
                    throw new Exception("argument[$item] is missing", 2000);
                }
            } else {
                throw new Exception("argument[$item] is missing", 2000);
            }
        }
    }
    
    /**
     * 进行http 签名校验
     * @param string $ctl
     * @param string $action
     * @param string $type
     */
    private function checkHttpSignParams($ctl,$action,$type){
        $ctl = strtolower($ctl);
        $action = strtolower($action);
        $type = strtolower($type);
        
        if(empty($_GET['ak'])){
            throw new Exception('GET.ak is required',Resbox_Errno::ERRNO_COMMON_PARAM_ERR);
        }
        $ak = $_GET['ak'];
        $req_method = $_SERVER['REQUEST_METHOD'];
        $data = array();
        switch ($req_method){
            case 'GET':
                $data = $_GET;
                break;
            case 'POST':
                $data = $_POST;
                break;
            default:
                $data = $_REQUEST;
                break;
        }
        $reqired_fields = array( 'ak','time','sign');
        foreach ($reqired_fields as $f){
            if(empty($data[$f])){
                throw new Exception($req_method.'.'.$f.' is required',Resbox_Errno::ERRNO_COMMON_PARAM_ERR);
            }
        }
        if($data['ak']!=$ak){
            throw new Exception('two ak?',Resbox_Errno::ERRNO_COMMON_REQUEST_ERR);
        }
        $isDebug = Bd_Conf::getIDC () == 'test' && !empty($_REQUEST['debug']);
        
        $_sign = $data['sign'];
        
        if(!$isDebug){
            $_time = $data['time'];
            if(!is_numeric($_time) || $_time < time() - 150 || $_time >time() + 150){
                throw new Exception('time is out of range',Resbox_Errno::ERRNO_COMMON_PARAM_ERR);
            }
            if(strlen($_sign)!=32){
                throw new Exception('sign length wrong',Resbox_Errno::ERRNO_COMMON_PARAM_ERR);
            }
        }
        
        $ak_files = array(
            $ctl.'/default',
            $ctl.'/'.$action.'_default',
            $ctl.'/'.$action.'/'.$type,
        );
        
        //3份配置会进行merge
        $ak_conf = array();
        foreach ($ak_files as $_path){
            $t_ak_conf=Bd_Conf::getAppConf('secert/'.$_path);
            if(empty($t_ak_conf) ){
                continue;
            }
            if(!is_array($t_ak_conf)){
                Bd_Log::warning('parse secert file failed: secert/'.$_path);
                continue;
            }
            $ak_conf = array_merge($ak_conf,$t_ak_conf);
        }
        
        if(empty($ak_conf)){
            throw new Exception('ak conf is empty',Resbox_Errno::ERRNO_COMMON_CONFIG_ERR);
        }
        if (empty($ak_conf[$ak])){
            throw new Exception('ak['.$ak.'] is not found',Resbox_Errno::ERRNO_COMMON_NO_PERMISSION);
        }
        
        $ak_info = $ak_conf[$ak];
        if(empty($ak_info['sk'])){
            throw new Exception('sk is empty',Resbox_Errno::ERRNO_COMMON_CONFIG_ERR);
        }
        
        if(!$isDebug){
            $_server_sign = Box_Util_Secert::genHttpSign($data, $ak_info['sk']);
            if($_server_sign !=$_sign){
                throw new Exception('sign not match',Resbox_Errno::ERRNO_COMMON_SIGN_ERR);
            }
        }
        
        if(!empty($ak_info['http_method']) && !in_array($req_method, $ak_info['http_method'])){
            throw new Exception('http method is not allowed',Resbox_Errno::ERRNO_COMMON_REQUEST_ERR);
        }
        //业务可以读取这个属性
        Resbox_Request::setAttribute('ak_info', $ak_info);
    }

    /**
     * 输出成功信息
     * @param string $result
     */
    public function success($result) {
        $ret = array(
            'errno' => 0,
            'errmsg' => '',
            'request_id'=> LOG_ID,
            '_used' => sprintf('%.3f',(microtime(true)-$this->_requestStartTime)*1000),
        );
        if (is_array($result)) {
            $ret['data'] = $result;
        }
        $this->echoResult($ret);
    }

    /**
     * 输出错误信息
     * @param string $errno
     * @param string $errmsg
     */
    public function error($errno, $errmsg = '') {
        $ret = array(
            'errno' => $errno,
            'errmsg' => $errmsg,
            'request_id'=> LOG_ID,
            '_used' => sprintf('%.3f',(microtime(true)-$this->_requestStartTime)*1000),
        );
       $this->echoResult($ret);
    }
    
    /**
     * 输出内容
     * @param array $ret
     */
    private function echoResult($ret){
        ob_clean();
        if($this->_enableJsonp){
            if($_SERVER["REQUEST_METHOD"]!="GET" || empty($_GET["cb"])){
                $this->_enableJsonp=false;
            }
            if($this->_enableJsonp && !preg_match("/^[_a-zA-Z][_a-zA-Z0-9]+$/", $_GET["cb"])){
                echo "cb param invalid";
                return;
            }
        }
        $jsonStr = json_encode($ret);
        
        Bd_Log::addNotice('resp_cut', mb_substr($jsonStr,0,100,'utf-8'));
        
        if($this->_enableJsonp){
            header('Content-Type: text/javascript;charset=utf-8');
            echo $_GET["cb"]."( ".$jsonStr." )";
        }else{
            header('Content-Type: application/json;charset=utf-8');
            echo $jsonStr;
        }
    }

}
