<?php
/**
 * Created by PhpStorm.
 * User: v_lishilong
 * Date: 2017/11/9
 * Time: 12:47
 */
class Utils_Ral_RestapiGolang
{
    /**
     * @var string
     */
    protected $service = 'mapi_im_restapigolang';

    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * 单例
     * @return null|Utils_Ral_RestapiGolang
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Utils_Ral_RestapiGolang();
        }
        return self::$instance;
    }

    /**
     * 获取关主流数据
     * @param $data
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function getStreamData($data, $throwException = true){
        $arrNeedKeys = array(
            'loc_city',
            'ip',
            'cuid',
            'network',
            'os_version',
            'sid',
            'app_version',
            'loc_district',
            'osname',
            'osbranch',
            'pkgname',
            'loc_province',
            'command',
            'ut',
            'ua',
            'front_type',
            'city_code',
            'refresh_type',
            'product',
            'refresh_state',
            'refresh_count',
            'channel_id',
            'blacklist_timestamp',
            'subscribe',
            'smfw',
            'filter',
            'baijiahao_sub',
            'loc_point',
            'context'
        );
        $inputs = Utils_Common::buildNeedInputs($arrNeedKeys, $data);
        return $this->request('content/1.0/stream', $inputs, $throwException);
    }




    /**
     * 请求
     * @param $pathInfo
     * @param $inputs
     * @param $throwException
     * @return bool
     * @throws Exception
     */
    private function request($pathInfo, $inputs, $throwException) {

        ral_set_logid(LOG_ID);
        ral_set_pathinfo($pathInfo);
        ral_set_querystring('');
        Bd_Log::addNotice('restapi_golang_path', $pathInfo);
        Utils_Debug::addDebugData('restapi_golang_request', $inputs);

        $ret = ral($this->service, 'post', $inputs, rand(),array('content-type' => "application/json"));

        Utils_Debug::addDebugData('restapi_golang_response', $ret);
        Bd_Log::debug("restapi golang request: " . json_encode($inputs));
        Bd_Log::debug("restapi golang response: " . json_encode($ret));

        if ($ret == false) {
            Bd_Log::addNotice('restapi_golang_error', 'ral error: ' . ral_get_error());
            if ($throwException) {
                throw new Exception("Request restapi_golang ral error: " . ral_get_error());
            } else {
                Bd_Log::warning("Request restapi_golang ral error: " . ral_get_error());
                return false;
            }
        } else {
            if ($ret['error_code'] != 0) {
                Bd_Log::addNotice('restapi_golang_error', $ret);
                Bd_Log::addNotice('restapi_golang_params', $ret);
                if ($throwException) {
                    throw new Exception("Request restapi_golang return: " . $ret['error_msg']);
                } else {
                    Bd_Log::warning("Request restapi_golang return: " . $ret['error_msg']);
                    return false;
                }
            }
        }

        return $ret['feed_list'];
    }
}