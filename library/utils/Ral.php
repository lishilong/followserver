<?php
/**
 * Ral Service 基础类 有单独需求请继承此类
 *
 * 在data层 对应两种data serivice: 存储 和 api
 * 本类对应于api类的data层
 *
 * 封装ral操作 鉴于逻辑相对简单，因此将增加dao层  和 data层合在了一起
 *
 */
class Utils_Ral {

    /**
     * 服务名称
     * @var string
     */
    protected $_service = '';

    /**
     * 基础路径
     * @var string
     */
    protected $_basePath = '';

    /**
     * 公共query
     * @var array
     */
    protected $_commonQuerystring = array();

    /**
     * 公共data
     * @var array
     */
    protected $_commonData = array();

    /**
     * 公共的header
     * @var array
     */
    protected $_commonHeader = array(

    );

    /**
     * 缓存池
     * @var array
     */
    protected static $_pool = array();


    /**
     * 构造函数
     * @param string $service    服务名称
     */
    public function __construct($service = null) {
        $service && $this->_service = $service;
    }


    /**
     * 工厂函数
     * @param string $service    服务名称
     * @return object $this     this
     */
    public static function factory($service = null){
        if (!isset(self::$_pool[$service])) {
            self::$_pool[$service] = new self($service);
        }
        return self::$_pool[$service];
    }


    /**
     *
     * @return array 请求结果
     */
    /**
     * 实际进行ral request的方法
     * @param  string $method      get/post 目前暂时不支持其他方法
     * @param  string $pathinfo    uri
     * @param  array $querystring  请求参数
     * @param  array $data         post数据
     * @param  array $header       header信息
     * @return mixed               返回结果
     */
    protected function request($method, $pathinfo, $querystring = array(), $data = array(), $header = array()) {
        $querystring && $querystring = array_merge($this->_commonQuerystring, $querystring);
        $header = array_merge($this->_commonHeader, $header);

        ral_set_pathinfo($this->_basePath . $pathinfo);
        ral_set_logid(LOG_ID);
        $querystring && ral_set_querystring(http_build_query($querystring));
        $result = ral($this->_service, $method, $data, rand(), $header);
        if ($result === false) {
            $args = array(
                'pathinfo'  => $this->_basePath . $pathinfo,
                'query'     => $querystring,
                'post'      => $data,
                'errno'     => ral_get_errno(),
                'errmsg'    => ral_get_error(),
                'protocol_code' => ral_get_protocol_code(),
            );
            //Mbd_Log_Txt::setWarning(500, $this->_service . ' ral returned false');
        }
        return $result;
    }

    /**
     * get 请求
     * @param  string $pathinfo    uri
     * @param  array $querystring  请求参数
     * @param  array $header       header信息
     * @return mixed               返回结果
     */
    public function get($pathinfo, $querystring = array(), $header = array()) {
        return $this->request('get', $pathinfo, $querystring, array(), $header);
    }

    /**
     * post 请求
     * @param  string $pathinfo    uri
     * @param  array $data         post数据
     * @param  array $querystring  请求参数
     * @param  array $header       header信息
     * @return mixed               返回结果
     */
    public function post($pathinfo, $data, $querystring = array(), $header = array()) {
        return $this->request('post', $pathinfo, $querystring, $data, $header);
    }

}
