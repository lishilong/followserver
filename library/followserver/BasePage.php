<?php
/**
 * Created by PhpStorm.
 * User: chenhanchuan
 * Date: 17/11/8
 * Time: 上午10:30
 */

abstract class Followserver_BasePage {

    /**
     * @var
     */
    protected $requests;

    /**
     * execute
     * @param $requests
     * @return bool
     */
    public function execute($requests) {

        $this->requests = $requests;
        $this->initialize();
        return $this->run();
    }

    /**
     * initialize
     */
    protected function initialize() {
        // do nothing
    }

    /**
     * run
     * @return bool
     */
    protected function run() {
        return true;
    }
}