<?php
/**
 * Created by PhpStorm.
 * User: chenhanchuan
 * Date: 17/11/8
 * Time: 上午10:36
 */

class Action_Index extends Ap_Action_Abstract {

    public function execute() {
        header('HTTP/1.1 404 Not Found');
        exit;
    }
}