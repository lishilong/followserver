<?php
/**
 * @filename: actions/follow/Follow.php
 * @desc
 * @author Weiyanjiang [weiyanjiang@baidu.com]
 * @create 2017-11-01 17:12:16
 * @last modify 2017-11-01 17:12:16
 */

class Action_Follow extends Ap_Action_Abstract {

	public function execute() {
		Followserver_Request::init();
		Bd_Log::addNotice('logid', Followserver_Request::getLogid());
		//1. check if user is login as needed
		//$arrUserinfo = Saf_SmartMain::getUserInfo();
		//if (empty($arrUserinfo)) {
		//    $bduss = $_COOKIE['BDUSS'];
		//    $arrUserinfo = Bd_Passport::checkUserLogin($bduss, 1);
		//}
		//2. get and validate input params
		$arrRequest = Saf_SmartMain::getCgi();
		//$arrRequest = Bd_String::iconv_recursive($arrRequest, 'gbk', 'utf8');
		//3. call PageService
		$objServicePagePa = new Service_Page_Common_Follow();
		$objServicePagePa->execute($arrRequest);

		//4. chage data to out format
		//$arrOutput = $arrPageInfo;

		//5. build page
		// smarty模板，以下渲染模板的代码依赖于提供一个tpl模板
		//$tpl = Bd_TplFactory::getInstance();
		//$tpl->assign($arrOutput);
		//$tpl->display('en/newapp/index.tpl');

		//这里直接输出,作为示例
		//$strOut = json_encode($arrOutput);
		$strOut = Followserver_Response::formatResponse();
		echo $strOut;

		//notice日志信息打印，只需要添加日志信息，saf会自动打一条log
		Bd_Log::addNotice('out', $strOut);

	}
}
