<?php

/**
 * Utils_Redis
 * redis得公共封装
 * @author libo
 * @version $Id: Redis.php 3668 2015-12-21 06:30:46Z baijianmin $
 * @see http://man.baidu.com/ksarch/store/redis/
 */
class Utils_Redis {
    /*
     * redis setex 方法
     */

    public static function set($key, $value, $expire = 3600) {

        //存入数据
        $input = array(
            'key' => $key,
            'value' => $value,
            'seconds' => $expire,
        );
        $res = Box_Api_Redis::call('SETEX', $input);

        if ($res[$key] == "OK") {
            return true;
        }
        return false;
    }

    /**
     * redis get方法
     */
    public static function get($key) {

        //获取数据
        $input = array(
            'key' => $key,
        );
        $res = Box_Api_Redis::call('GET', $input);
        if (isset($res[$key])) {
            return $res[$key];
        }
        return null;
    }

    /**
     * redis del方法
     */
    public static function del($key) {

        //获取数据
        $input = array(
            'key' => $key,
        );
        $res = Box_Api_Redis::call('DEL', $input);
        if (isset($res[$key])) {
            return $res[$key];
        }
        return false;
    }

    /**
     * redis ttl方法
     */
    public static function ttl($key) {

        //获取数据
        $input = array(
            'key' => $key,
        );
        $res = Box_Api_Redis::call('TTL', $input);
        return json_decode($res, true);
    }

    /**
     * redis sadd 方法
     * @param $key
     * @param $members
     * @return bool
     */
    public static function sadd($key, $members) {

        //存入数据
        $input = array(
            'key' => $key,
            'member' => $members,
        );
        $res = Box_Api_Redis::call('SADD', $input);

        if ($res[$key]) {
            return true;
        }
        return false;
    }

    /**
     * redis srem 方法
     * @param $key
     * @param $members
     * @return bool
     */
    public static function srem($key, $members) {

        //存入数据
        $input = array(
            'key' => $key,
            'member' => $members,
        );
        $res = Box_Api_Redis::call('SREM', $input);
        if ($res[$key]) {
            return true;
        }
        return false;
    }

    /**
     * redis smembers 方法
     * @param $key
     * @return array
     */
    public static function smembers($key) {
        //获取数据
        $input = array(
            'key' => $key,
        );
        $res = Box_Api_Redis::call('SMEMBERS', $input);
        if (isset($res[$key])) {
            return $res[$key];
        }
        return null;
    }

    /**
     * 自增1
     * @param type $key
     * @return boolean
     */
    public static function incr($key) {
        $input = array(
            'key' => $key,
        );
        $res = Box_Api_Redis::call('INCR', $input);
        if (isset($res[$key])) {
            return $res[$key];
        }
        return false;
    }

    /**
     * 向列表中插入元素
     * @param type $key
     * @param array $values
     * @return boolean | int
     */
    public static function lpush($key, array $values) {
        $input = array(
            'key' => $key,
            'value' => $values,
        );
        $res = Box_Api_Redis::call('LPUSH', $input);
        if (isset($res[$key])) {
            return $res[$key];
        }
        return false;
    }

    public static function lrange($key, $start = 1, $stop = -1) {
        $input = array(
            'key' => $key,
            'start' => $start,
            'stop' => $stop,
        );
        $res = Box_Api_Redis::call('LRANGE', $input);
        if (isset($res[$key])) {
            return $res[$key];
        }
        return false;
    }

    /**
     * redis expire 方法
     * @param $key
     * @param $ttl
     * @return array
     */
    public static function expire($key, $ttl = 3600) {
        //获取数据
        $input = array(
            'key' => $key,
            'seconds' => $ttl,
        );
        $res = Box_Api_Redis::call('EXPIRE', $input);

        if (isset($res[$key])) {
            return true;
        }
        return false;
    }

}
