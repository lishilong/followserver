<?php
/**
 * @filename: controllers/Main.php
 * @desc 主控制器,也是默认控制器
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:12:35
 * @last modify 2017-11-01 17:12:35
 */

class Controller_Follow extends Ap_Controller_Abstract {
	public $actions = array(
		'follow' => 'actions/follow/Follow.php',
	);
}
