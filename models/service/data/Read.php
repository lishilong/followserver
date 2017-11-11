<?php
/**
 * @desc 阅读数
 * Created by PhpStorm.
 * User: v_lishilong
 * Date: 2017/11/8
 * Time: 16:17
 */
class Service_Data_Read extends Service_Data_Base
{
    /**
     * 获取阅读数
     * @param $array
     * @return array
     */
      public function getReadNumber($array)
      {
            $arr = $array();
            if(!is_array($array) || count($array) == 0)
            {
                  return $arr;
            }

            $result = Box_Social_Celebrity_InteractiveData::mgetViewCount($array);

            if(is_array($result) &&  !empty($result['mget']))
            {
                  $arr = $result['mget'];
            }

            return $arr;
      }
}