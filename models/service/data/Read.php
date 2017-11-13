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
        $newArrayNids = $this->_multiAddDtPrefix($arrayNids);

        $result = Box_Social_Celebrity_InteractiveData::mgetViewCount($newArrayNids);

        if(is_array($result) &&  !empty($result['mget']))
        {
             //删除数组KEY的前缀
              $arr = $this->_multiRemoveDtPrefix($result['mget']);
        }

        return $arr;
      }

    /**
     * 拼装数组前缀dt_
     * @param $arrayNids
     * @return array
     */
    private function _multiAddDtPrefix($arrayNids)
    {
        $list = array();

        foreach($arrayNids as $val){
            $str = substr($val,0,3);
            if($str == 'dt_'){
                $list[] = $val;
            }else{
                $list[] = 'dt_'.$val;
            }
        }

        return $list;
    }


    /**
     * 去除数组KEY前缀dt_
     * @param $arrayMget
     * @return array
     */
    private function _multiRemoveDtPrefix($arrayMget)
    {
        $data = array();

        foreach($arrayMget as $key => $val)
        {
            $str = substr($key,0,3);
            if($str == "dt_")
            {
                $newKey = substr($key,3);
                $data[$newKey] = $val;
            }else{
                $data[$key] = $val;
            }
        }

        return $data;
    }

}