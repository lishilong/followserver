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
     * @param $arrayNids
     * @return array
     */
    public function getReadNumber($arrayNids)
    {
        $arr = array();
        if(!is_array($arrayNids) || count($arrayNids) == 0)
        {
              return $arr;
        }

        //拼装数组
        $newArrayNids = $this->addArrayPrefix($arrayNids);

        $result = Box_Social_Celebrity_InteractiveData::mgetViewCount($newArrayNids);

        if(is_array($result) &&  !empty($result['mget']))
        {
             //删除数组KEY的前缀
              $arr = $this->delArrayPrefix($result['mget']);
        }

        return $arr;
      }
}