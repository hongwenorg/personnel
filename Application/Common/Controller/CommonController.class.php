<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:公共方法模板控制器，让其他控制器继承
*/
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize () {
		//判断是否有uid
		if(empty($_SESSION)){ 
			$this->error("请先登录！","/index.php");
		}
	}
}
?>