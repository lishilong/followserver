<?php

/*
 * 时间记录类
 * @author libo
 * @version $Id: Timer.php 240125 2015-04-17 05:22:18Z scmpf $
 */
define('TIMER_START', 0);
define('TIMER_STOP', 1);

class Utils_Timer {

    private static $_timer_group = null;
    private static $_mapping = array(
        'db' => 0,
    );

    /**
     * 初始化
     */
    public static function init() {
        self::$_timer_group = new Bd_TimerGroup();
        self::$_timer_group->start('totalTimer');
    }

    /**
     * 返回记录结果
     */
    public static function decompose() {
        self::$_timer_group->stop('totalTimer');
        $arrTime = self::$_timer_group->getTotalTime();
        return $arrTime;
    }

    /**
     * 操作
     * @param int $type
     * @param int $operation
     * @return boolean
     */
    public static function timer_operation($type, $operation) {
        if (!in_array($operation, array(TIMER_START, TIMER_STOP))) {
            return false;
        }
        if ($operation == TIMER_START) {
            $action = 'start';
        }
        if ($operation == TIMER_STOP) {
            $action = 'stop';
        }
        if (in_array($type, array_keys(self::$_mapping))) {
            self::$_timer_group->{$action}($type . "Timer");
            return true;
        }
        return false;
    }

}
