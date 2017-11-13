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


    /**
     * 拼装数组前缀dt_
     * @param $arrayNids
     * @return array
     */
    public function addArrayPrefix($arrayNids)
    {
        $list = array();

        foreach($arrayNids as $val){
            $str = substr($val,0,3);
            if($str == 'dt_'){
                $list[] = $val;
            }else{
                $list[] = 'dt_'.$val;
            }
        }

        return $list;
    }


    /**
     * 去除数组KEY前缀
     * @param $arrayMget
     * @return array
     */
    public function delArrayPrefix($arrayMget)
    {
        $data = array();

        foreach($arrayMget as $key => $val)
        {
            $str = substr($val,0,3);
            if($str == "dt_")
            {
                $newKey = substr($key,3);
                $data[$newKey] = $val;
            }else{
                $data[$key] = $val;
            }
        }

        return $data;
    }

}
