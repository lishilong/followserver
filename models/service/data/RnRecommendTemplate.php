<?php
/**
 * Created by PhpStorm.
 * User: v_lishilong
 * Date: 2017/11/14
 * Time: 11:20
 */
class Server_Data_RnRecommendTemplate{

    private $_ext = null;
    // V标图片
    private $_vurl = array(
        0 => '', //none
        1 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460792824.png', //golden
        2 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460970589.png', //blue
        3 => 'https://b.bdstatic.com/searchbox/image/cmsuploader/20170719/1500448460903468.png', //yellow
    );



    /**
     * rn横划模板
     * @return bool
     */
    private function recommendTemplate()
    {
        $this->_tpl['itemType'] = 'follow_recommend';
        $this->_tpl['title'] = '为你推荐';
        $this->_tpl['source'] = '手机百度';

        $cmd = '';
        $rid = isset($this->_meta['id']) ? $this->_meta['id'] : '';
        $text = isset($this->_meta['content']['account_name']) ? $this->_meta['content']['account_name'] : '';

        //判断meta中字段ext是否存在数据
        if(!is_array($this->_meta['content']['ext']) && empty($this->_meta['content']['ext']))
        {
            return false;
        }
        $this->_ext = $this->_meta['content']['ext'];

        $image = isset($this->_ext['account_img']) ? $this->_ext['account_img'] : '';
        $describe = isset($this->_ext['account_desc']) ? $this->_ext['account_desc'] : '';
        $vtype = isset($this->_ext['account_vtype']) ? $this->_ext['account_vtype'] : '';
        $vurl = isset($this->_vurl[$vtype]) ? $this->_vurl[$vtype] : '';
        $type = isset($this->_ext['account_type']) ? $this->_ext['account_type'] : '';

        //待确定字段名称
        $thirdid = isset($this->_ext['account_thirdid']) ? $this->_ext['account_thirdid'] : '';

        $this->_tpl['itemData'] = array(
            'rid' => $rid,
            'image' => $image,
            'title' => array(
                'text' => $text,
                'align' => 'center'
            ),
            'vtype' => $vtype,
            'vurl' => $vurl,
            'desc' => array(
                'text' => $describe,
                'align' => 'center',
                'color' => '#F0F0F0'
            ),
            'cmd' => $cmd,
            'follow' => array(
                'type' => $type,
                'thirdid' => $thirdid,
                'button' => array(
                    'state' => '0',
                    'data' =>  array(
                        'text' => '关注',
                        'size' => '11',
                        'color' => '#FFFFFF',
                        'colorskin' => '#000000',
                        'bgcolor' => '',
                        'bgcolorskin' => '',
                        'bgcolortaped' => '#6C85E8',
                        'bold' => '0',
                        'api' => ''
                    )
                )
            )
        );


        return true;
    }

}