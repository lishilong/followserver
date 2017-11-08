<?php

/**
 * action 基类
 * @author baijianmin
 * @ctime 2015-6-4
 */
abstract class Followserver_BaseAction extends Ap_Action_Abstract {

    /**
     * execute
     */
    public function execute() {

        try {
            $requests = Saf_SmartMain::getCgi();

            $result = $this->run($requests);
            $this->success($result);
        } catch (Exception $exception) {
            $this->error($exception);
        }
    }

    /**
     * run
     * @param $requests
     * @return bool
     */
    protected function run($requests) {

        return true;
    }

    /**
     * 成功返回
     * @param null $array
     */
    protected function success($array = null) {

        $arrSuccess = array(
            'errno'     => 0,
            'requestid' => LOG_ID,
            'h'         => $_SERVER['SERVER_ADDR'],
        );
        Bd_Log::addNotice('err_no', 0);

        if (is_array($array) && !empty($array)) {
            $arrSuccess['data'] = $array;
        }

        echo json_encode($arrSuccess);
    }

    /**
     * 失败返回
     * @param Exception $exception
     */
    protected function error(Exception $exception) {

        $errNo = $exception->getCode();
        $errMsg = $exception->getMessage();
        $line = $exception->getLine();
        $file = $exception->getFile();

        Bd_Log::addNotice('err_no', $errNo);
        Bd_Log::addNotice('err_msg', $errMsg);
        Bd_Log::addNotice('err_line', $line);
        Bd_Log::addNotice('err_file', $file);
        Bd_Log::warning('', $errNo);


        $arrFail = array(
            'errno' => $errNo,
            'requestid' => LOG_ID,
            'errmsg' => $errMsg,
            'h' => $_SERVER['SERVER_ADDR'],
        );

        echo json_encode($arrFail);
    }

}
