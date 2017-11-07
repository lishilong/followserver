<?php
/**
 * @filename: models/service/data/Comment.php
 * @desc 获取评论数据，详见：http://wiki.baidu.com/pages/viewpage.action?pageId=207968303
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-06 11:48:26
 * @last modify 2017-11-06 11:48:26
 */

class Service_Data_Comment extends Service_Data_Base
{
    const COMMENT_APPID = 103;

    public $commentFactory;

    /**
     * @desc
     * @param 
     * @return 
     */
    public function __construct($resource){
        $this->setFactory();
    }

    /**
     * @desc
     * @param 
     * @return 
     */
    public function execute()
    {
    }
   
    public function setFactory() {
        $this->commentFactory = Box_Comment_ThreadApi::factory(self::COMMENT_APPID);
    }
    
}
