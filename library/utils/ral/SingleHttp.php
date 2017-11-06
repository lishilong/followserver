<?php
/**
 * Utils_Ral_SingleHttp
 *
 * RAL HTTP 单进程调用外部服务，支持GET和POST两种方式
 *
 * @$resource 参数组，按key说明如下：
        service: string类型，表示服务名称，要求与ral配置文件中要保持一致
        method : string类型，get或post方式
        input  : Array类型，http请求的参数组，按照key-value组合即可
        header : Array类型，http协议中的头信息，格式举例如下：
            array(
                'pathinfo' => "index.php",
                'querystring'=> "user=robin&id=1",
                'useragent' => "Mozilla/5.0",
                'referer' => "http://www.baidu.com",
                'content-type' => "application/octet-stream",
                'cookie' => array('uid'=>'1234'),
                'Accept-Encoding' => "gzip",
                'Host' => "www.baidu.com",
             )
 *
 *
 *
 *
 * @author wuziyang@baidu.com
 * @version 1.0
 */
class Utils_Ral_SingleHttp{

    public $ret;
    private $service;
    private $method;
    private $input;
    private $header;
    private $extra;

    public function __construct($resource){
        $this->setResource($resource);
    }

    public function setResource($resource){
        $this->service = !empty($resource['service']) ? $resource['service'] : '';
        $this->method = !empty($resource['method']) ? $resource['method'] : 'get';
        $this->input = !empty($resource['input']) ? $resource['input'] : array();
        $this->header = !empty($resource['header']) ? $resource['header'] : array();
        $this->extra = !empty($resource['extra']) ? $resource['extra'] : 1;
    }

    public function request($logid=null){
        if(isset($logid)){
            ral_set_logid($logid);
        }
        //服务名为必传参数
        if(empty($this->service)){
            return false;
        }
        if( Utils_Trace::getSwitch() == 1 && $this->service == 'bigbox' && is_array($this->header) ){
            $this->header['querystring'] .= '&trace=1';
        }
        $this->ret = ral($this->service, $this->method, $this->input, $this->extra,$this->header);
        Utils_Trace::setUserVars(
            array('ral_response' => $this->ret, 'ral_request' => array($this->service, $this->method, $this->input, $this->extra,$this->header)),
            '',
            'ral_service'
        );
        return $this->ret;
    }

    //出错消息
    public function getError(){
        return ral_get_error();
    }

    //错误号
    public function getErrno(){
        return ral_get_errno();
    }

    //请求后返回的http协议状态码
    public function getProtocolCode(){
        return ral_get_protocol_code();
    }

}
