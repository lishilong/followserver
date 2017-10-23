<?php

/**
 * 
 * @author baijianmin
 * @version $Id$
 */
class Plugin_StatLog extends Ap_Plugin_Abstract {

    /**
     * dispatchLoopShutdown
     * @param Ap_Request_Abstract $request
     * @param Ap_Response_Abstract $response
     */
    public function dispatchLoopShutdown(Ap_Request_Abstract $request, Ap_Response_Abstract $response) {
        $arrTime = Utils_Timer::decompose();
        foreach ($arrTime as $tk => $timer) {
            Bd_Log::addNotice($tk, $timer);
        }
        Box_Log_Txt::printLog();
        Bd_Log::notice('');
    }

}
