<?php

/**
 * 
 * @author baijianmin
 * @version $Id$
 */
class Plugin_StatLog extends Ap_Plugin_Abstract {

    /**
     * @param Ap_Request_Abstract $request
     * @param Ap_Response_Abstract $response
     */
    public function dispatchLoopStartup(Ap_Request_Abstract $request, Ap_Response_Abstract $response) {

        Utils_Timer::init();
    }

    /**
     * @param Ap_Request_Abstract $request
     * @param Ap_Response_Abstract $response
     */
    public function dispatchLoopShutdown(Ap_Request_Abstract $request, Ap_Response_Abstract $response)
    {
        Utils_Timer::timer_operation('total', TIMER_STOP);
        // 日志公用字段, add字段优先级低，不会覆盖之前相同的字段
        $params = Saf_SmartMain::getCgi();
        $_controller = isset($params['controller']) ? $params['controller'] : '';
        $_action = isset($params['action']) ? $params['action'] : '';
        $necessary = array(
            'controller' => $_controller,
            'action'     => $_action,
        );
        foreach($necessary as $key => $value) {
            Bd_Log::addNotice($key, $value);
        }
        Utils_Timer::decompose();
    }

}
