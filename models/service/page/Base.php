<?php

/**
 * pagesrv基类
 * @author baijianmin
 * @version $Id$
 */
class Service_Page_Base {
    protected $pbLog = array();
    
    protected $enableJSONP=false;
    
    private $_errmsg = '';
    private $_errno = 0;
    /**
     * 
     * @param int $errno
     * @param string $errmsg
     */
    protected function errReturn($errno, $errmsg) {
        $this->_errno = $errno;
        $this->_errmsg = $errmsg;
        return false;
    }
    
    /**
     * 
     * @return number|int
     */
    public function getErrno() {
        return $this->_errno;
    }
    
    /**
     * 
     * @return string
     */
    public function getErrmsg() {
        return $this->_errmsg;
    }
    /**
     * @return boolean
     */
    public function isEnableJsonp(){
        return $this->enableJSONP;
    }
    
    /**
     * execute前执行
     */
    public function preExecute(){
        
    }
    
    /**
     * 打印日志，在page层设置 pbLog字段 即可
     * @param
     * @return bool
     */
    public function teardown(){
        //打印pb日志

        if(!$this->pbLog){
            return true;
        }
        if($this->pbLog['uid']){
            $userInfo['userid'] = $this->pbLog['uid'];
            
            Mbd_Log_Txt::setUid($this->pbLog['uid']);
        }
        if($this->pbLog['cuid']){
            $deviceInfo['cuid'] = $this->pbLog['cuid'];
            
            Mbd_Log_Txt::setCuid($this->pbLog['cuid']);
        }
        if($this->pbLog['baiduid']){
            $cookieUserId['baiduid'] =$this->pbLog['baiduid'];
        }
        if($this->_errno){
            Mbd_Log_Pb::setErrno($this->_errno);
        }

        $httpServiceInfo = array(
            'user_agent' =>  $_SERVER['HTTP_USER_AGENT'],
            'cookie'     =>  $_SERVER['HTTP_COOKIE'],
            'request_url'=>  $_SERVER['REQUEST_URI'],
            'http_method'=>  $_SERVER['REQUEST_METHOD'],
        );


        Mbd_Log_Pb::setPassportId($userInfo);
        Mbd_Log_Pb::setDeviceId($deviceInfo);
        Mbd_Log_Pb::setCookieUserid($cookieUserId);
        Mbd_Log_Pb::setHttpServiceInfo($httpServiceInfo);

        Mbd_Log_Pb::setActionData(json_encode($this->pbLog));
        Mbd_Log_Txt::setNotice('udata', json_encode($this->pbLog));
    }
}
