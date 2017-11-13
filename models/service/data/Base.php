<?php

/**
 * 
 * @author baijianmin
 * @version $Id$
 */
class Service_Data_Base {

    private $_errmsg = '';
    private $_errno =0;
    
    /**
     * 
     * @param string $msg
     */
    protected function setErrmsg($msg) {
        $this->_errmsg = $msg;
    }
    
    /**
     * 
     * @return string
     */
    public function getErrmsg() {
        return $this->_errmsg;
    }
    
    /**
     * 
     * @param int $errno
     * @param string $error
     */
    protected function setError($errno,$error){
        $this->_errno=$errno;
        $this->_errmsg=$error;
    }
    
    /**
     * 
     * @return number
     */
    public function getErrNo(){
        return $this->_errno;
    }


}
